<?php

declare(strict_types=1);

namespace TCM\Controllers\Api;

use TCM\Core\Controller;
use TCM\Core\Auth;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Core\Validator;
use TCM\Core\WhatsApp;
use TCM\Core\Mailer;
use TCM\Models\Course;
use TCM\Models\Event;
use TCM\Models\Lead;
use TCM\Models\Post;
use TCM\Models\Program;
use TCM\Models\Testimonial;

/**
 * Read-only + lightweight write endpoints consumed by the public static site.
 */
final class PublicController extends Controller
{
    /**
     * Auth state + site config for the static front-end (auth-aware nav, WhatsApp).
     */
    public function me(): void
    {
        $user = Auth::user();
        Response::success([
            'authenticated'   => $user !== null,
            'name'            => $user['name'] ?? null,
            'role'            => $user['role'] ?? null,
            'dashboard_url'   => $user === null ? null : ($user['role'] === 'admin' ? '/admin' : '/student'),
            'whatsapp_number' => WhatsApp::number(),
            'csrf_token'      => \TCM\Core\Csrf::token(),
        ], 'OK');
    }

    public function courses(): void
    {
        $courses = Course::listWithCategory([
            'status'   => 'published',
            'audience' => Request::string('audience') ?: null,
            'search'   => Request::string('q') ?: null,
            'featured' => Request::string('featured') ?: null,
        ]);
        foreach ($courses as &$course) {
            $course['discount_percent'] = Course::discountPercent($course);
        }
        Response::success($courses, 'Courses fetched.');
    }

    public function course(array $params): void
    {
        $course = Course::findBySlug($params['slug']);
        if ($course === null) {
            Response::error('Course not found.', 404);
        }
        $course['curriculum'] = Course::curriculum((int) $course['id']);
        $course['reviews'] = Database::all(
            "SELECT cr.rating, cr.comment, cr.created_at, u.name
             FROM course_reviews cr JOIN users u ON u.id = cr.user_id
             WHERE cr.course_id = ? AND cr.status = 'approved' ORDER BY cr.created_at DESC",
            [(int) $course['id']]
        );
        Response::success($course, 'Course fetched.');
    }

    public function events(): void
    {
        $events = Event::search([
            'status'   => Request::string('status') ?: null,
            'type'     => Request::string('type') ?: null,
            'category' => Request::string('category') ?: null,
            'search'   => Request::string('q') ?: null,
        ]);
        foreach ($events as &$event) {
            $event['seats_left'] = Event::seatsLeft($event);
        }
        Response::success($events, 'Events fetched.');
    }

    public function programs(): void
    {
        $programs = Program::search([
            'status' => 'published',
            'type'   => Request::string('type') ?: null,
            'search' => Request::string('q') ?: null,
        ]);
        Response::success($programs, 'Programs fetched.');
    }

    public function posts(): void
    {
        Response::success(Post::published(), 'Posts fetched.');
    }

    public function testimonials(): void
    {
        Response::success(Testimonial::active(), 'Testimonials fetched.');
    }

    public function contact(): void
    {
        $data = Request::all();
        $validator = Validator::make($data, [
            'name'    => 'required|max:120',
            'email'   => 'required|email',
            'message' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            Response::error('Please check your details.', 422, $validator->errors());
        }

        Database::insert('contact_messages', [
            'name'    => Request::string('name'),
            'email'   => Request::string('email'),
            'subject' => Request::string('subject'),
            'message' => Request::string('message'),
            'status'  => 'new',
        ]);
        Lead::capture([
            'name'          => Request::string('name'),
            'email'         => Request::string('email'),
            'phone'         => Request::string('phone') ?: null,
            'interest_type' => 'contact',
            'message'       => Request::string('message'),
            'source'        => 'contact-form',
        ]);

        $name = Request::string('name');
        $email = Request::string('email');
        $admin = (string) config('mail.admin_email');
        if ($admin !== '') {
            Mailer::send(
                $admin,
                'New contact message from ' . $name,
                Mailer::template('New contact message', sprintf(
                    '<p><strong>%s</strong> &lt;%s&gt; %s</p><p><em>%s</em></p><p>%s</p>',
                    e($name), e($email), e(Request::string('phone') ?: ''),
                    e(Request::string('subject') ?: 'General'),
                    nl2br(e(Request::string('message')))
                ))
            );
        }
        Mailer::send(
            $email,
            'We received your message — The Code Munk',
            Mailer::template('Thanks for reaching out, ' . $name . '!',
                '<p>We have received your message and will reply within 24 hours.</p>')
        );

        Response::success(null, 'Thanks for reaching out! We will reply soon.');
    }

    public function subscribe(): void
    {
        $email = strtolower(Request::string('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('A valid email is required.', 422);
        }
        Database::run(
            'INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)',
            [$email]
        );
        Response::success(null, 'Subscribed! Watch your inbox for updates.');
    }
}
