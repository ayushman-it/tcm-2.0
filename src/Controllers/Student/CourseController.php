<?php

declare(strict_types=1);

namespace TCM\Controllers\Student;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\WhatsApp;
use TCM\Models\Course;
use TCM\Models\Enrollment;
use TCM\Models\Lead;

final class CourseController extends Controller
{
    public function browse(): void
    {
        $user = Auth::require('student');
        $courses = Course::listWithCategory([
            'status'   => 'published',
            'audience' => Request::string('audience') ?: null,
            'search'   => Request::string('q') ?: null,
        ]);

        // Mark which courses the student already owns.
        $owned = array_column(Enrollment::forUser((int) $user['id']), 'course_id');

        $this->view('student/courses/browse', [
            'title'   => 'Browse Courses',
            'courses' => $courses,
            'owned'   => $owned,
        ], 'student');
    }

    public function show(array $params): void
    {
        $user = Auth::require('student');
        $course = Course::findBySlug($params['slug']);
        if ($course === null) {
            flash('error', 'Course not found.');
            redirect('/student/courses');
        }
        $this->view('student/courses/show', [
            'title'      => $course['title'],
            'course'     => $course,
            'curriculum' => Course::curriculum((int) $course['id']),
            'enrolled'   => Enrollment::exists((int) $user['id'], (int) $course['id']),
        ], 'student');
    }

    /**
     * Enroll in a course. Free courses enrol instantly; paid courses capture a
     * lead and hand the student off to WhatsApp to finalise (no online payment).
     */
    public function purchase(array $params): void
    {
        $user = Auth::require('student');
        $course = Course::find((int) $params['id']);
        if ($course === null) {
            flash('error', 'Course not found.');
            redirect('/student/courses');
        }

        if (Enrollment::exists((int) $user['id'], (int) $course['id'])) {
            flash('error', 'You are already enrolled in this course.');
            redirect('/student/learn/' . $course['id']);
        }

        // Free course -> enrol immediately.
        if ((float) $course['price'] <= 0) {
            Enrollment::enroll((int) $user['id'], (int) $course['id']);
            flash('success', 'Enrolled in ' . $course['title'] . '. Happy learning!');
            redirect('/student/learn/' . $course['id']);
        }

        // Paid course -> capture a lead and route to WhatsApp.
        Lead::capture([
            'user_id'        => (int) $user['id'],
            'name'           => $user['name'],
            'email'          => $user['email'],
            'phone'          => $user['phone'] ?? null,
            'interest_type'  => 'course',
            'interest_id'    => (int) $course['id'],
            'interest_title' => $course['title'],
            'source'         => 'student-dashboard',
        ]);

        if (WhatsApp::isConfigured()) {
            redirect(WhatsApp::link(WhatsApp::enquiryMessage($course['title'], $user['name'], 'course')));
        }
        flash('success', 'Thanks! Our team will reach out to help you enrol in ' . $course['title'] . '.');
        redirect('/student/courses/' . $course['slug']);
    }

    /**
     * Course learning view with progress tracking.
     */
    public function learn(array $params): void
    {
        $user = Auth::require('student');
        $courseId = (int) $params['id'];
        if (!Enrollment::exists((int) $user['id'], $courseId)) {
            flash('error', 'Enroll first to access this course.');
            redirect('/student/courses');
        }
        $course = Course::find($courseId);

        $completed = array_column(
            Database::all(
                'SELECT lesson_id FROM lesson_progress WHERE user_id = ? AND completed = 1',
                [(int) $user['id']]
            ),
            'lesson_id'
        );

        $this->view('student/courses/learn', [
            'title'      => $course['title'] ?? 'Course',
            'course'     => $course,
            'curriculum' => Course::curriculum($courseId),
            'completed'  => array_map('intval', $completed),
        ], 'student');
    }

    /**
     * Toggle lesson completion and recompute course progress.
     */
    public function toggleLesson(array $params): void
    {
        $user = Auth::require('student');
        $lessonId = (int) $params['lessonId'];

        $existing = Database::first(
            'SELECT * FROM lesson_progress WHERE user_id = ? AND lesson_id = ?',
            [(int) $user['id'], $lessonId]
        );

        if ($existing) {
            $new = (int) $existing['completed'] === 1 ? 0 : 1;
            Database::update('lesson_progress', [
                'completed'    => $new,
                'completed_at' => $new ? date('Y-m-d H:i:s') : null,
            ], ['id' => $existing['id']]);
        } else {
            Database::insert('lesson_progress', [
                'user_id'      => (int) $user['id'],
                'lesson_id'    => $lessonId,
                'completed'    => 1,
                'completed_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $courseId = (int) ($params['id'] ?? 0);
        $this->recomputeProgress((int) $user['id'], $courseId);

        $this->respond(null, 'Progress updated.', '/student/learn/' . $courseId);
    }

    private function recomputeProgress(int $userId, int $courseId): void
    {
        $total = (int) Database::scalar(
            'SELECT COUNT(*) FROM course_lessons l
             JOIN course_modules m ON m.id = l.module_id
             WHERE m.course_id = ?',
            [$courseId]
        );
        if ($total === 0) {
            return;
        }
        $done = (int) Database::scalar(
            'SELECT COUNT(*) FROM lesson_progress p
             JOIN course_lessons l ON l.id = p.lesson_id
             JOIN course_modules m ON m.id = l.module_id
             WHERE p.user_id = ? AND m.course_id = ? AND p.completed = 1',
            [$userId, $courseId]
        );
        $percent = (int) round($done / $total * 100);

        if ($percent >= 100) {
            Database::run(
                "UPDATE enrollments SET progress = 100, status = 'completed', completed_at = NOW()
                 WHERE user_id = ? AND course_id = ?",
                [$userId, $courseId]
            );
            $this->issueCertificate($userId, $courseId);
        } else {
            Database::run(
                'UPDATE enrollments SET progress = ? WHERE user_id = ? AND course_id = ?',
                [$percent, $userId, $courseId]
            );
        }
    }

    private function issueCertificate(int $userId, int $courseId): void
    {
        $exists = (int) Database::scalar(
            'SELECT COUNT(*) FROM certificates WHERE user_id = ? AND course_id = ?',
            [$userId, $courseId]
        );
        if ($exists > 0) {
            return;
        }
        $course = Course::find($courseId);
        Database::insert('certificates', [
            'user_id'            => $userId,
            'course_id'          => $courseId,
            'certificate_number' => 'TCM-CERT-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)),
            'title'              => 'Certificate of Completion - ' . ($course['title'] ?? 'Course'),
        ]);
    }
}
