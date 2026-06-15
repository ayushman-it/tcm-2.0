<?php

declare(strict_types=1);

namespace TCM\Controllers\Student;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\WhatsApp;
use TCM\Models\Lead;
use TCM\Models\Program;

final class ProgramController extends Controller
{
    public function browse(): void
    {
        $user = Auth::require('student');
        $programs = Program::search([
            'status' => 'published',
            'type'   => Request::string('type') ?: null,
            'search' => Request::string('q') ?: null,
        ]);
        $joined = array_column(
            Database::all('SELECT program_id FROM program_enrollments WHERE user_id = ?', [(int) $user['id']]),
            'program_id'
        );

        $this->view('student/programs/browse', [
            'title'    => 'Programs',
            'programs' => $programs,
            'joined'   => array_map('intval', $joined),
        ], 'student');
    }

    public function show(array $params): void
    {
        $user = Auth::require('student');
        $program = Program::findBySlug($params['slug']);
        if ($program === null) {
            flash('error', 'Program not found.');
            redirect('/student/programs');
        }
        $joined = (int) Database::scalar(
            'SELECT COUNT(*) FROM program_enrollments WHERE user_id = ? AND program_id = ?',
            [(int) $user['id'], (int) $program['id']]
        ) > 0;

        $this->view('student/programs/show', [
            'title'    => $program['title'],
            'program'  => $program,
            'courses'  => Program::courses((int) $program['id']),
            'sessions' => Program::sessions((int) $program['id']),
            'joined'   => $joined,
        ], 'student');
    }

    /**
     * Express interest in a program -> capture lead + WhatsApp handoff.
     */
    public function enquire(array $params): void
    {
        $user = Auth::require('student');
        $program = Program::find((int) $params['id']);
        if ($program === null) {
            flash('error', 'Program not found.');
            redirect('/student/programs');
        }

        // Free internships etc. enrol immediately.
        if ((float) $program['price'] <= 0) {
            Database::run(
                'INSERT IGNORE INTO program_enrollments (user_id, program_id, status) VALUES (?, ?, ?)',
                [(int) $user['id'], (int) $program['id'], 'active']
            );
            flash('success', 'You have joined ' . $program['title'] . '.');
            redirect('/student/programs/' . $program['slug']);
        }

        Lead::capture([
            'user_id'        => (int) $user['id'],
            'name'           => $user['name'],
            'email'          => $user['email'],
            'phone'          => $user['phone'] ?? null,
            'interest_type'  => 'program',
            'interest_id'    => (int) $program['id'],
            'interest_title' => $program['title'],
            'source'         => 'student-dashboard',
        ]);

        if (WhatsApp::isConfigured()) {
            redirect(WhatsApp::link(WhatsApp::enquiryMessage($program['title'], $user['name'], $program['type'])));
        }
        flash('success', 'Thanks! Our team will reach out about ' . $program['title'] . '.');
        redirect('/student/programs/' . $program['slug']);
    }
}
