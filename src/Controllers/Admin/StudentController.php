<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\Enrollment;
use TCM\Models\EventRegistration;

final class StudentController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $q = Request::string('q');
        $sql = "SELECT id, name, email, phone, status, onboarded, created_at
                FROM users WHERE role = 'student'";
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (name LIKE ? OR email LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        $sql .= ' ORDER BY created_at DESC';

        $this->view('admin/students/index', [
            'title'    => 'Students',
            'students' => Database::all($sql, $params),
        ], 'admin');
    }

    public function show(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $student = Database::first("SELECT * FROM users WHERE id = ? AND role = 'student'", [$id]);
        if ($student === null) {
            flash('error', 'Student not found.');
            redirect('/admin/students');
        }
        $profile = Database::first('SELECT * FROM student_profiles WHERE user_id = ?', [$id]);

        $this->view('admin/students/show', [
            'title'         => $student['name'],
            'student'       => $student,
            'profile'       => $profile,
            'enrollments'   => Enrollment::forUser($id),
            'registrations' => EventRegistration::forUser($id),
            'orders'        => Database::all('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC', [$id]),
        ], 'admin');
    }

    public function toggleStatus(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $student = Database::first('SELECT status FROM users WHERE id = ?', [$id]);
        if ($student !== null) {
            $new = $student['status'] === 'active' ? 'suspended' : 'active';
            Database::update('users', ['status' => $new], ['id' => $id]);
        }
        $this->respond(null, 'Student status updated.', '/admin/students/' . $id);
    }
}
