<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\InternshipApplication;
use TCM\Models\Program;

final class InternshipController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $this->view('admin/internships/index', [
            'title'        => 'Internship Applications',
            'applications' => InternshipApplication::search([
                'status'     => Request::string('status') ?: null,
                'program_id' => Request::int('program_id') ?: null,
                'search'     => Request::string('q') ?: null,
            ]),
            'programs'     => Database::all("SELECT id, title FROM programs WHERE type = 'internship' ORDER BY title"),
            'counts'       => [
                'submitted'   => InternshipApplication::count("status = 'submitted'"),
                'shortlisted' => InternshipApplication::count("status = 'shortlisted'"),
                'selected'    => InternshipApplication::count("status = 'selected'"),
            ],
        ], 'admin');
    }

    public function show(array $params): void
    {
        Auth::require('admin');
        $app = Database::first(
            'SELECT a.*, p.title AS program_title, u.name AS student_name, u.email AS student_email
             FROM internship_applications a
             JOIN programs p ON p.id = a.program_id
             JOIN users u ON u.id = a.user_id
             WHERE a.id = ?',
            [(int) $params['id']]
        );
        if ($app === null) {
            flash('error', 'Application not found.');
            redirect('/admin/internships');
        }
        $this->view('admin/internships/show', [
            'title' => 'Application: ' . $app['full_name'],
            'app'   => $app,
        ], 'admin');
    }

    public function update(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $status = Request::string('status', 'under_review');
        if (!array_key_exists($status, InternshipApplication::STATUSES)) {
            $status = 'under_review';
        }
        InternshipApplication::update($id, [
            'status'      => $status,
            'notes'       => Request::string('notes'),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);
        $this->respond(null, 'Application updated.', '/admin/internships/' . $id);
    }

    /**
     * Stream a resume from private storage (admin only — not web-accessible).
     */
    public function downloadResume(array $params): void
    {
        Auth::require('admin');
        $app = InternshipApplication::find((int) $params['id']);
        if ($app === null || empty($app['resume_file'])) {
            http_response_code(404);
            echo 'Resume not found.';
            return;
        }
        // Guard against path traversal: only allow a bare filename.
        $name = basename((string) $app['resume_file']);
        $path = (string) config('uploads.private_path') . '/resumes/' . $name;
        if (!is_file($path)) {
            http_response_code(404);
            echo 'Resume file is missing.';
            return;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
        $downloadName = preg_replace('/[^A-Za-z0-9]+/', '_', (string) $app['full_name']) . '_resume.' . $ext;

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . (string) filesize($path));
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}
