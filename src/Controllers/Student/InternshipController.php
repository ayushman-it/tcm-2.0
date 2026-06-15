<?php

declare(strict_types=1);

namespace TCM\Controllers\Student;

use RuntimeException;
use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Upload;
use TCM\Models\InternshipApplication;
use TCM\Models\Program;

final class InternshipController extends Controller
{
    public function applications(): void
    {
        $user = Auth::require('student');
        $this->view('student/internships/index', [
            'title'        => 'My Applications',
            'applications' => InternshipApplication::forUser((int) $user['id']),
        ], 'student');
    }

    public function showForm(array $params): void
    {
        $user = Auth::require('student');
        $program = Program::find((int) $params['id']);
        if ($program === null || $program['type'] !== 'internship') {
            flash('error', 'Internship not found.');
            redirect('/student/programs');
        }
        if (InternshipApplication::exists((int) $user['id'], (int) $program['id'])) {
            flash('error', 'You have already applied to this internship.');
            redirect('/student/applications');
        }
        $profile = Database::first('SELECT * FROM student_profiles WHERE user_id = ?', [$user['id']]) ?? [];

        $this->view('student/internships/apply', [
            'title'   => 'Apply: ' . $program['title'],
            'program' => $program,
            'user'    => $user,
            'profile' => $profile,
        ], 'student');
    }

    public function apply(array $params): void
    {
        $user = Auth::require('student');
        $program = Program::find((int) $params['id']);
        if ($program === null || $program['type'] !== 'internship') {
            flash('error', 'Internship not found.');
            redirect('/student/programs');
        }
        $backTo = '/student/internships/' . $program['id'] . '/apply';

        if (InternshipApplication::exists((int) $user['id'], (int) $program['id'])) {
            flash('error', 'You have already applied to this internship.');
            redirect('/student/applications');
        }

        $this->validate([
            'full_name'     => 'required|min:2|max:150',
            'email'         => 'required|email',
            'phone'         => 'required|min:7|max:20',
            'why'           => 'required|min:20',
            'portfolio_url' => 'url',
        ], $backTo);

        // Resume upload (required)
        $resumeFile = $_FILES['resume'] ?? null;
        if (!Upload::present($resumeFile)) {
            $_SESSION['_old'] = Request::all();
            flash('error', 'Please attach your resume (PDF/DOC/DOCX).');
            redirect($backTo);
        }
        try {
            $stored = Upload::store(
                $resumeFile,
                (string) config('uploads.private_path') . '/resumes',
                (array) config('uploads.resume_allowed', ['pdf', 'doc', 'docx'])
            );
        } catch (RuntimeException $e) {
            $_SESSION['_old'] = Request::all();
            flash('error', $e->getMessage());
            redirect($backTo);
        }

        InternshipApplication::create([
            'user_id'       => (int) $user['id'],
            'program_id'    => (int) $program['id'],
            'full_name'     => Request::string('full_name'),
            'email'         => Request::string('email'),
            'phone'         => Request::string('phone'),
            'college'       => Request::string('college'),
            'skills'        => Request::string('skills'),
            'why'           => Request::string('why'),
            'portfolio_url' => Request::string('portfolio_url'),
            'resume_file'   => $stored,
            'status'        => 'submitted',
        ]);

        // Acknowledge the applicant and notify the team.
        \TCM\Core\Mailer::send(
            Request::string('email'),
            'Application received — ' . $program['title'],
            \TCM\Core\Mailer::template('Thanks for applying, ' . Request::string('full_name') . '!',
                '<p>We have received your application for <strong>' . e($program['title'])
                . '</strong>. Our team will review it and get back to you soon.</p>')
        );
        $admin = (string) config('mail.admin_email');
        if ($admin !== '') {
            \TCM\Core\Mailer::send(
                $admin,
                'New internship application: ' . $program['title'],
                \TCM\Core\Mailer::template('New internship application', sprintf(
                    '<p><strong>%s</strong> (%s, %s) applied for <strong>%s</strong>.</p><p><a href="%s">Review applications</a></p>',
                    e(Request::string('full_name')), e(Request::string('email')), e(Request::string('phone')),
                    e($program['title']), base_url('/admin/internships')
                ))
            );
        }

        flash('success', 'Application submitted for ' . $program['title'] . '. We will review and reach out.');
        redirect('/student/applications');
    }
}
