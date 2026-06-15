<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\WhatsApp;
use TCM\Models\Enrollment;
use TCM\Models\EventRegistration;
use TCM\Models\Lead;
use TCM\Models\Order;

final class LeadController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $leads = Lead::search([
            'status' => Request::string('status') ?: null,
            'search' => Request::string('q') ?: null,
        ]);
        // Attach a ready-to-use WhatsApp reply link per lead (admin -> lead).
        foreach ($leads as &$lead) {
            $lead['wa_link'] = $lead['phone']
                ? WhatsApp::link(
                    'Hi ' . $lead['name'] . ', thanks for your interest in '
                    . ($lead['interest_title'] ?? 'The Code Munk') . '!',
                    $lead['phone']
                )
                : null;
        }

        $this->view('admin/leads/index', [
            'title' => 'Leads',
            'leads' => $leads,
            'counts' => [
                'new'       => Lead::count("status = 'new'"),
                'contacted' => Lead::count("status = 'contacted'"),
                'converted' => Lead::count("status = 'converted'"),
            ],
        ], 'admin');
    }

    public function updateStatus(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $status = Request::string('status', 'contacted');
        if (in_array($status, ['new', 'contacted', 'converted', 'lost'], true)) {
            Lead::update($id, ['status' => $status]);
        }
        $this->respond(null, 'Lead updated.', '/admin/leads');
    }

    /**
     * Convert a lead into a real enrolment/registration once payment is
     * settled offline, and record a paid order for reporting.
     */
    public function convert(array $params): void
    {
        Auth::require('admin');
        $lead = Lead::find((int) $params['id']);
        if ($lead === null) {
            flash('error', 'Lead not found.');
            redirect('/admin/leads');
        }
        if ($lead['user_id'] === null) {
            flash('error', 'This lead has no linked student account. Ask them to register first.');
            redirect('/admin/leads');
        }

        $userId = (int) $lead['user_id'];
        $itemId = (int) ($lead['interest_id'] ?? 0);

        switch ($lead['interest_type']) {
            case 'course':
                $course = Database::first('SELECT * FROM courses WHERE id = ?', [$itemId]);
                if ($course) {
                    $orderId = Order::place($userId, 'course', $itemId, $course['title'], (float) $course['price']);
                    Enrollment::enroll($userId, $itemId, $orderId);
                }
                break;
            case 'event':
                $event = Database::first('SELECT * FROM events WHERE id = ?', [$itemId]);
                if ($event) {
                    Order::place($userId, 'event', $itemId, $event['title'], (float) $event['price']);
                    EventRegistration::register($itemId, $userId);
                }
                break;
            case 'program':
                Database::run(
                    'INSERT IGNORE INTO program_enrollments (user_id, program_id, status) VALUES (?, ?, ?)',
                    [$userId, $itemId, 'active']
                );
                break;
        }

        Lead::update((int) $lead['id'], ['status' => 'converted']);
        $this->respond(null, 'Lead converted and student enrolled.', '/admin/leads');
    }

    public function destroy(array $params): void
    {
        Auth::require('admin');
        Lead::delete((int) $params['id']);
        $this->respond(null, 'Lead deleted.', '/admin/leads');
    }
}
