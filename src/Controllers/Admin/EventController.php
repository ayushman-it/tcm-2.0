<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\Event;

final class EventController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $events = Event::search(['search' => Request::string('q') ?: null]);
        $this->view('admin/events/index', [
            'title'  => 'Events',
            'events' => $events,
        ], 'admin');
    }

    public function create(): void
    {
        Auth::require('admin');
        $this->view('admin/events/form', ['title' => 'New Event', 'event' => null], 'admin');
    }

    public function store(): void
    {
        Auth::require('admin');
        $this->validate([
            'title'    => 'required|min:3|max:180',
            'category' => 'in:frontend,backend,python,dsa,career,general',
            'type'     => 'in:free,paid',
            'status'   => 'in:upcoming,ongoing,past',
        ], '/admin/events/create');

        $slug = $this->uniqueSlug(slugify(Request::string('title')));
        $id = Event::create($this->payload($slug));
        $this->respond(['id' => $id], 'Event created.', '/admin/events');
    }

    public function edit(array $params): void
    {
        Auth::require('admin');
        $event = Event::find((int) $params['id']);
        if ($event === null) {
            flash('error', 'Event not found.');
            redirect('/admin/events');
        }
        $registrations = Database::all(
            'SELECT r.*, u.name, u.email FROM event_registrations r
             JOIN users u ON u.id = r.user_id WHERE r.event_id = ? ORDER BY r.registered_at DESC',
            [(int) $event['id']]
        );
        $this->view('admin/events/form', [
            'title'         => 'Edit Event',
            'event'         => $event,
            'registrations' => $registrations,
        ], 'admin');
    }

    public function update(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $event = Event::find($id);
        if ($event === null) {
            flash('error', 'Event not found.');
            redirect('/admin/events');
        }
        $this->validate([
            'title'  => 'required|min:3|max:180',
            'type'   => 'in:free,paid',
            'status' => 'in:upcoming,ongoing,past',
        ], '/admin/events/' . $id . '/edit');

        $slug = $event['slug'];
        if (Request::string('title') !== $event['title']) {
            $slug = $this->uniqueSlug(slugify(Request::string('title')), $id);
        }
        Event::update($id, $this->payload($slug));
        $this->respond(['id' => $id], 'Event updated.', '/admin/events/' . $id . '/edit');
    }

    public function destroy(array $params): void
    {
        Auth::require('admin');
        Event::delete((int) $params['id']);
        $this->respond(null, 'Event deleted.', '/admin/events');
    }

    /**
     * @return array<string,mixed>
     */
    private function payload(string $slug): array
    {
        return [
            'title'        => Request::string('title'),
            'slug'         => $slug,
            'description'  => Request::string('description'),
            'category'     => Request::string('category', 'general'),
            'type'         => Request::string('type', 'free'),
            'status'       => Request::string('status', 'upcoming'),
            'price'        => (float) Request::string('price', '0'),
            'event_date'   => Request::string('event_date') ?: null,
            'event_time'   => Request::string('event_time') ?: null,
            'mode'         => Request::string('mode', 'online'),
            'location'     => Request::string('location'),
            'total_seats'  => Request::int('total_seats', 16),
            'recording_url' => Request::string('recording_url') ?: null,
        ];
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;
        while (true) {
            $sql = 'SELECT COUNT(*) FROM events WHERE slug = ?';
            $params = [$slug];
            if ($ignoreId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $ignoreId;
            }
            if ((int) Database::scalar($sql, $params) === 0) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }
}
