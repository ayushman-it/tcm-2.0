<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');

        $stats = [
            'students'      => (int) Database::scalar("SELECT COUNT(*) FROM users WHERE role = 'student'"),
            'courses'       => (int) Database::scalar('SELECT COUNT(*) FROM courses'),
            'events'        => (int) Database::scalar('SELECT COUNT(*) FROM events'),
            'enrollments'   => (int) Database::scalar('SELECT COUNT(*) FROM enrollments'),
            'revenue'       => (float) Database::scalar("SELECT COALESCE(SUM(amount),0) FROM orders WHERE status = 'paid'"),
            'new_messages'  => (int) Database::scalar("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'"),
        ];

        $recentOrders = Database::all(
            'SELECT o.*, u.name AS student_name FROM orders o
             JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC LIMIT 8'
        );

        $recentStudents = Database::all(
            "SELECT id, name, email, created_at FROM users WHERE role = 'student'
             ORDER BY created_at DESC LIMIT 8"
        );

        $this->view('admin/dashboard', [
            'title'          => 'Dashboard',
            'user'           => Auth::user(),
            'stats'          => $stats,
            'recentOrders'   => $recentOrders,
            'recentStudents' => $recentStudents,
        ], 'admin');
    }
}
