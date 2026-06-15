<?php

declare(strict_types=1);

namespace TCM\Controllers\Student;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Models\Enrollment;
use TCM\Models\EventRegistration;
use TCM\Models\LiveSession;
use TCM\Models\Portfolio;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::require('student');
        if ((int) $user['onboarded'] === 0) {
            redirect('/student/onboarding');
        }

        $enrollments = Enrollment::forUser((int) $user['id']);
        $registrations = EventRegistration::forUser((int) $user['id']);

        $this->view('student/dashboard', [
            'title'             => 'My Dashboard',
            'user'              => $user,
            'enrollments'       => $enrollments,
            'registrations'     => $registrations,
            'liveSessions'      => LiveSession::upcomingForUser((int) $user['id']),
            'portfolioStrength' => Portfolio::strength((int) $user['id']),
            'certificates'      => Portfolio::certificates((int) $user['id']),
        ], 'student');
    }
}
