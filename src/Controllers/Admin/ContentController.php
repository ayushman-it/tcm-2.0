<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\Testimonial;

/**
 * Handles ancillary CMS content: testimonials, categories, contact messages
 * and site settings.
 */
final class ContentController extends Controller
{
    // --- Categories ------------------------------------------------------
    public function categories(): void
    {
        Auth::require('admin');
        $this->view('admin/categories/index', [
            'title'      => 'Categories',
            'categories' => Database::all('SELECT * FROM categories ORDER BY sort_order, name'),
        ], 'admin');
    }

    public function storeCategory(): void
    {
        Auth::require('admin');
        $this->validate(['name' => 'required|min:2|max:120'], '/admin/categories');
        Database::insert('categories', [
            'name'       => Request::string('name'),
            'slug'       => slugify(Request::string('name')),
            'description' => Request::string('description'),
            'icon'       => Request::string('icon', 'bi-collection'),
            'audience'   => Request::string('audience', 'general'),
            'sort_order' => Request::int('sort_order'),
            'status'     => 'active',
        ]);
        $this->respond(null, 'Category added.', '/admin/categories');
    }

    public function deleteCategory(array $params): void
    {
        Auth::require('admin');
        Database::delete('categories', ['id' => (int) $params['id']]);
        $this->respond(null, 'Category removed.', '/admin/categories');
    }

    // --- Testimonials ----------------------------------------------------
    public function testimonials(): void
    {
        Auth::require('admin');
        $this->view('admin/testimonials/index', [
            'title'        => 'Testimonials',
            'testimonials' => Testimonial::all('sort_order, id'),
        ], 'admin');
    }

    public function storeTestimonial(): void
    {
        Auth::require('admin');
        $this->validate([
            'name'    => 'required|max:120',
            'content' => 'required',
        ], '/admin/testimonials');
        Testimonial::create([
            'name'       => Request::string('name'),
            'role'       => Request::string('role'),
            'content'    => Request::string('content'),
            'rating'     => min(5, max(1, Request::int('rating', 5))),
            'status'     => 'active',
            'sort_order' => Request::int('sort_order'),
        ]);
        $this->respond(null, 'Testimonial added.', '/admin/testimonials');
    }

    public function deleteTestimonial(array $params): void
    {
        Auth::require('admin');
        Testimonial::delete((int) $params['id']);
        $this->respond(null, 'Testimonial removed.', '/admin/testimonials');
    }

    // --- Contact messages ------------------------------------------------
    public function messages(): void
    {
        Auth::require('admin');
        Database::run("UPDATE contact_messages SET status = 'read' WHERE status = 'new'");
        $this->view('admin/messages/index', [
            'title'    => 'Messages',
            'messages' => Database::all('SELECT * FROM contact_messages ORDER BY created_at DESC'),
        ], 'admin');
    }

    public function deleteMessage(array $params): void
    {
        Auth::require('admin');
        Database::delete('contact_messages', ['id' => (int) $params['id']]);
        $this->respond(null, 'Message deleted.', '/admin/messages');
    }

    // --- Settings --------------------------------------------------------
    public function settings(): void
    {
        Auth::require('admin');
        $rows = Database::all('SELECT * FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        $this->view('admin/settings/index', [
            'title'    => 'Site Settings',
            'settings' => $settings,
        ], 'admin');
    }

    public function saveSettings(): void
    {
        Auth::require('admin');
        $allowed = ['site_name', 'site_tagline', 'contact_email', 'contact_phone', 'students_count', 'courses_count', 'currency', 'whatsapp_number', 'whatsapp_message'];
        foreach ($allowed as $key) {
            $value = Request::string($key);
            Database::run(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                [$key, $value]
            );
        }
        $this->respond(null, 'Settings saved.', '/admin/settings');
    }
}
