<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\WhatsApp;
use TCM\Models\Event;
use TCM\Models\Course;
use TCM\Models\Lead;

final class LeadFormController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $forms = Database::all(
            'SELECT lf.*, u.name AS creator_name
             FROM lead_forms lf LEFT JOIN users u ON u.id = lf.created_by
             ORDER BY lf.created_at DESC'
        );
        $this->view('admin/lead-forms/index', [
            'title' => 'Lead Forms',
            'forms' => $forms,
        ], 'admin');
    }

    public function create(): void
    {
        Auth::require('admin');
        $this->view('admin/lead-forms/form', [
            'title'    => 'New Lead Form',
            'form'     => null,
            'events'   => Event::search([]),
            'courses'  => Course::listWithCategory(['status' => 'published']),
        ], 'admin');
    }

    public function store(): void
    {
        Auth::require('admin');
        $this->validate([
            'title' => 'required|min:2|max:200',
        ], '/admin/lead-forms/create');

        $slug = $this->uniqueSlug(slugify(Request::string('title')));
        $id   = Database::insert('lead_forms', $this->payload($slug));

        $this->respond(['id' => $id], 'Lead form created.', '/admin/lead-forms/' . $id . '/edit');
    }

    public function edit(array $params): void
    {
        Auth::require('admin');
        $form = Database::first('SELECT * FROM lead_forms WHERE id = ?', [(int)$params['id']]);
        if ($form === null) {
            flash('error', 'Form not found.');
            redirect('/admin/lead-forms');
        }
        $this->view('admin/lead-forms/form', [
            'title'   => 'Edit Lead Form',
            'form'    => $form,
            'events'  => Event::search([]),
            'courses' => Course::listWithCategory(['status' => 'published']),
        ], 'admin');
    }

    public function update(array $params): void
    {
        Auth::require('admin');
        $id   = (int)$params['id'];
        $form = Database::first('SELECT * FROM lead_forms WHERE id = ?', [$id]);
        if ($form === null) {
            flash('error', 'Form not found.');
            redirect('/admin/lead-forms');
        }
        $this->validate(['title' => 'required|min:2|max:200'], '/admin/lead-forms/' . $id . '/edit');

        $slug = $form['slug'];
        if (Request::string('title') !== $form['title']) {
            $slug = $this->uniqueSlug(slugify(Request::string('title')), $id);
        }
        Database::update('lead_forms', $this->payload($slug), ['id' => $id]);
        $this->respond(['id' => $id], 'Form updated.', '/admin/lead-forms/' . $id . '/edit');
    }

    public function destroy(array $params): void
    {
        Auth::require('admin');
        Database::delete('lead_forms', ['id' => (int)$params['id']]);
        $this->respond(null, 'Form deleted.', '/admin/lead-forms');
    }

    /** Public shareable form page — no auth required */
    public function show(array $params): void
    {
        $form = Database::first(
            "SELECT * FROM lead_forms WHERE slug = ? AND status = 'active' LIMIT 1",
            [$params['slug']]
        );
        if ($form === null) {
            http_response_code(404);
            echo '<h1 style="font-family:sans-serif;padding:40px;">Form not found.</h1>';
            exit;
        }

        // Track view
        Database::run('UPDATE lead_forms SET views = views + 1 WHERE id = ?', [$form['id']]);

        $fields = json_decode($form['fields_json'] ?? '[]', true) ?: $this->defaultFields();
        $this->view('lead-form/show', [
            'title' => $form['title'],
            'form'  => $form,
            'fields' => $fields,
        ], null);
    }

    /** Handle submission from public form */
    public function submit(array $params): void
    {
        $form = Database::first(
            "SELECT * FROM lead_forms WHERE slug = ? AND status = 'active' LIMIT 1",
            [$params['slug']]
        );
        if ($form === null) {
            http_response_code(404);
            exit;
        }

        $fields = json_decode($form['fields_json'] ?? '[]', true) ?: $this->defaultFields();

        // Collect submitted values
        $name    = trim(Request::string('name')  ?: Request::string('full_name') ?: 'Unknown');
        $email   = strtolower(trim(Request::string('email')));
        $phone   = trim(Request::string('phone') ?: Request::string('mobile') ?: '');
        $message = trim(Request::string('message') ?: '');

        // Build extra fields string for lead message
        $extras = [];
        foreach ($fields as $f) {
            $fieldName = $f['name'] ?? '';
            if (in_array($fieldName, ['name','full_name','email','phone','mobile'], true)) continue;
            $val = trim(Request::string($fieldName));
            if ($val !== '') {
                $extras[] = ($f['label'] ?? $fieldName) . ': ' . $val;
            }
        }
        if ($extras) {
            $message = implode(' | ', $extras) . ($message ? ' | ' . $message : '');
        }

        // Capture lead
        Lead::capture([
            'name'           => $name,
            'email'          => $email ?: null,
            'phone'          => $phone ?: null,
            'interest_type'  => $form['context_type'] ?? 'general',
            'interest_id'    => $form['context_id']   ? (int)$form['context_id'] : null,
            'interest_title' => $form['context_title'] ?: $form['title'],
            'message'        => $message ?: null,
            'source'         => 'lead-form:' . $form['slug'],
        ]);

        // Increment submissions
        Database::run(
            'UPDATE lead_forms SET submissions = submissions + 1 WHERE id = ?',
            [$form['id']]
        );

        // WhatsApp redirect
        if ((int)$form['whatsapp_redirect'] && WhatsApp::isConfigured()) {
            $waMsg = 'Hi The Code Munk! I am interested in ' . ($form['context_title'] ?: $form['title'])
                   . '. My name is ' . $name
                   . ($phone ? ', contact: ' . $phone : '')
                   . '.';
            redirect(WhatsApp::link($waMsg));
        }

        // Thank you page
        redirect('/form/' . $form['slug'] . '/thank-you');
    }

    public function thankYou(array $params): void
    {
        $form = Database::first('SELECT * FROM lead_forms WHERE slug = ?', [$params['slug']]);
        $this->view('lead-form/thank-you', [
            'title' => 'Thank You!',
            'form'  => $form,
        ], null);
    }

    // ── Helpers ──────────────────────────────────────────────────────── //

    private function payload(string $slug): array
    {
        // Build fields JSON from form builder inputs
        $fieldNames   = Request::all()['field_name']    ?? [];
        $fieldLabels  = Request::all()['field_label']   ?? [];
        $fieldTypes   = Request::all()['field_type']    ?? [];
        $fieldReq     = Request::all()['field_required'] ?? [];

        $fields = [];
        if (is_array($fieldNames)) {
            foreach ($fieldNames as $i => $fname) {
                $fname = trim((string)$fname);
                if ($fname === '') continue;
                $fields[] = [
                    'name'     => $fname,
                    'label'    => trim((string)($fieldLabels[$i] ?? $fname)),
                    'type'     => (string)($fieldTypes[$i] ?? 'text'),
                    'required' => isset($fieldReq[$i]) && $fieldReq[$i] === '1',
                ];
            }
        }

        // Fallback to defaults if none defined
        if (empty($fields)) {
            $fields = $this->defaultFields();
        }

        $contextType  = Request::string('context_type', 'general');
        $contextId    = Request::int('context_id') ?: null;
        $contextTitle = Request::string('context_title') ?: null;

        // Auto-fetch context title if not provided
        if ($contextId && !$contextTitle) {
            if ($contextType === 'event') {
                $row = Database::first('SELECT title FROM events WHERE id = ?', [$contextId]);
                $contextTitle = $row['title'] ?? null;
            } elseif ($contextType === 'course') {
                $row = Database::first('SELECT title FROM courses WHERE id = ?', [$contextId]);
                $contextTitle = $row['title'] ?? null;
            }
        }

        return [
            'title'              => Request::string('title'),
            'slug'               => $slug,
            'description'        => Request::string('description'),
            'context_type'       => $contextType,
            'context_id'         => $contextId,
            'context_title'      => $contextTitle,
            'fields_json'        => json_encode($fields, JSON_UNESCAPED_UNICODE),
            'cta_text'           => Request::string('cta_text', 'Submit'),
            'whatsapp_redirect'  => Request::int('whatsapp_redirect', 1),
            'thank_you_message'  => Request::string('thank_you_message', 'Thank you! We will be in touch shortly.'),
            'status'             => Request::string('status', 'active'),
            'created_by'         => Auth::id(),
        ];
    }

    private function defaultFields(): array
    {
        return [
            ['name' => 'name',    'label' => 'Full Name',     'type' => 'text',  'required' => true],
            ['name' => 'email',   'label' => 'Email Address', 'type' => 'email', 'required' => true],
            ['name' => 'phone',   'label' => 'Phone Number',  'type' => 'tel',   'required' => false],
            ['name' => 'message', 'label' => 'Message',       'type' => 'textarea', 'required' => false],
        ];
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i    = 1;
        while (true) {
            $sql    = 'SELECT COUNT(*) FROM lead_forms WHERE slug = ?';
            $params = [$slug];
            if ($ignoreId !== null) { $sql .= ' AND id <> ?'; $params[] = $ignoreId; }
            if ((int)Database::scalar($sql, $params) === 0) return $slug;
            $slug = $base . '-' . (++$i);
        }
    }
}
