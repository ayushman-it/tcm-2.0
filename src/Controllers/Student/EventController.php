<?php

declare(strict_types=1);

namespace TCM\Controllers\Student;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Request;
use TCM\Core\WhatsApp;
use TCM\Models\Event;
use TCM\Models\EventRegistration;
use TCM\Models\Lead;

final class EventController extends Controller
{
    public function browse(): void
    {
        $user = Auth::require('student');
        $events = Event::search([
            'status'   => Request::string('status') ?: null,
            'type'     => Request::string('type') ?: null,
            'category' => Request::string('category') ?: null,
            'search'   => Request::string('q') ?: null,
        ]);
        $joined = array_column(EventRegistration::forUser((int) $user['id']), 'event_id');

        $this->view('student/events/browse', [
            'title'  => 'Events',
            'events' => $events,
            'joined' => array_map('intval', $joined),
        ], 'student');
    }

    public function join(array $params): void
    {
        $user = Auth::require('student');
        $event = Event::find((int) $params['id']);
        if ($event === null) {
            flash('error', 'Event not found.');
            redirect('/student/events');
        }
        if ($event['status'] === 'past') {
            flash('error', 'This event has already ended.');
            redirect('/student/events');
        }
        if (Event::isFull($event)) {
            flash('error', 'Sorry, this event is full.');
            redirect('/student/events');
        }
        if (EventRegistration::exists((int) $event['id'], (int) $user['id'])) {
            flash('error', 'You are already registered for this event.');
            redirect('/student/events');
        }

        // Paid event -> capture lead + hand off to WhatsApp.
        if ($event['type'] === 'paid' && (float) $event['price'] > 0) {
            Lead::capture([
                'user_id'        => (int) $user['id'],
                'name'           => $user['name'],
                'email'          => $user['email'],
                'phone'          => $user['phone'] ?? null,
                'interest_type'  => 'event',
                'interest_id'    => (int) $event['id'],
                'interest_title' => $event['title'],
                'source'         => 'student-dashboard',
            ]);
            if (WhatsApp::isConfigured()) {
                redirect(WhatsApp::link(WhatsApp::enquiryMessage($event['title'], $user['name'], 'event')));
            }
            flash('success', 'Thanks! Our team will reach out about ' . $event['title'] . '.');
            redirect('/student/events');
        }

        // Free event -> register immediately.
        EventRegistration::register((int) $event['id'], (int) $user['id']);
        flash('success', 'You are registered for ' . $event['title'] . '.');
        redirect('/student/events');
    }
}
