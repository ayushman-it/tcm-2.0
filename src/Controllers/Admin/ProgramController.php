<?php

declare(strict_types=1);

namespace TCM\Controllers\Admin;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\Course;
use TCM\Models\Program;

final class ProgramController extends Controller
{
    public function index(): void
    {
        Auth::require('admin');
        $this->view('admin/programs/index', [
            'title'    => 'Programs',
            'programs' => Program::search(['search' => Request::string('q') ?: null]),
        ], 'admin');
    }

    public function create(): void
    {
        Auth::require('admin');
        $this->view('admin/programs/form', [
            'title'         => 'New Program',
            'program'       => null,
            'allCourses'    => Course::all('title'),
            'linkedCourses' => [],
            'sessions'      => [],
        ], 'admin');
    }

    public function store(): void
    {
        Auth::require('admin');
        $this->validate([
            'title'  => 'required|min:3|max:180',
            'type'   => 'in:live_classes,learning_track,internship,summer_campus,bootcamp,bundle',
            'price'  => 'numeric',
            'status' => 'in:draft,published,archived',
        ], '/admin/programs/create');

        $slug = $this->uniqueSlug(slugify(Request::string('title')));
        $id = Program::create($this->payload($slug));
        Program::syncCourses($id, (array) Request::input('courses', []));

        $this->respond(['id' => $id], 'Program created.', '/admin/programs/' . $id . '/edit');
    }

    public function edit(array $params): void
    {
        Auth::require('admin');
        $program = Program::find((int) $params['id']);
        if ($program === null) {
            flash('error', 'Program not found.');
            redirect('/admin/programs');
        }
        $linked = array_column(Program::courses((int) $program['id']), 'id');
        $this->view('admin/programs/form', [
            'title'         => 'Edit Program',
            'program'       => $program,
            'allCourses'    => Course::all('title'),
            'linkedCourses' => array_map('intval', $linked),
            'sessions'      => Program::sessions((int) $program['id']),
        ], 'admin');
    }

    public function update(array $params): void
    {
        Auth::require('admin');
        $id = (int) $params['id'];
        $program = Program::find($id);
        if ($program === null) {
            flash('error', 'Program not found.');
            redirect('/admin/programs');
        }
        $this->validate([
            'title'  => 'required|min:3|max:180',
            'type'   => 'in:live_classes,learning_track,internship,summer_campus,bootcamp,bundle',
            'status' => 'in:draft,published,archived',
        ], '/admin/programs/' . $id . '/edit');

        $slug = $program['slug'];
        if (Request::string('title') !== $program['title']) {
            $slug = $this->uniqueSlug(slugify(Request::string('title')), $id);
        }
        Program::update($id, $this->payload($slug));
        Program::syncCourses($id, (array) Request::input('courses', []));

        $this->respond(['id' => $id], 'Program updated.', '/admin/programs/' . $id . '/edit');
    }

    public function destroy(array $params): void
    {
        Auth::require('admin');
        Program::delete((int) $params['id']);
        $this->respond(null, 'Program deleted.', '/admin/programs');
    }

    // --- Live sessions ---------------------------------------------------
    public function addSession(array $params): void
    {
        Auth::require('admin');
        $programId = (int) $params['id'];
        Database::insert('live_sessions', [
            'program_id'  => $programId,
            'title'       => Request::string('title', 'Live Session'),
            'description' => Request::string('description'),
            'host'        => Request::string('host'),
            'session_date' => Request::string('session_date') ?: null,
            'start_time'  => Request::string('start_time') ?: null,
            'end_time'    => Request::string('end_time') ?: null,
            'meeting_url' => Request::string('meeting_url'),
            'status'      => Request::string('status', 'scheduled'),
        ]);
        $this->respond(null, 'Session added.', '/admin/programs/' . $programId . '/edit');
    }

    public function deleteSession(array $params): void
    {
        Auth::require('admin');
        $sessionId = (int) $params['sessionId'];
        $row = Database::first('SELECT program_id FROM live_sessions WHERE id = ?', [$sessionId]);
        Database::delete('live_sessions', ['id' => $sessionId]);
        $this->respond(null, 'Session removed.', '/admin/programs/' . ($row['program_id'] ?? '') . '/edit');
    }

    /**
     * Update a live session's status and recording link (mark live/completed).
     */
    public function updateSession(array $params): void
    {
        Auth::require('admin');
        $sessionId = (int) $params['sessionId'];
        $row = Database::first('SELECT program_id FROM live_sessions WHERE id = ?', [$sessionId]);
        if ($row === null) {
            flash('error', 'Session not found.');
            redirect('/admin/programs');
        }
        $status = Request::string('status', 'scheduled');
        if (!in_array($status, ['scheduled', 'live', 'completed', 'cancelled'], true)) {
            $status = 'scheduled';
        }
        Database::update('live_sessions', [
            'status'        => $status,
            'meeting_url'   => Request::string('meeting_url') ?: null,
            'recording_url' => Request::string('recording_url') ?: null,
        ], ['id' => $sessionId]);

        $this->respond(null, 'Session updated.', '/admin/programs/' . $row['program_id'] . '/edit');
    }

    /**
     * @return array<string,mixed>
     */
    private function payload(string $slug): array
    {
        return [
            'title'          => Request::string('title'),
            'slug'           => $slug,
            'subtitle'       => Request::string('subtitle'),
            'description'    => Request::string('description'),
            'type'           => Request::string('type', 'live_classes'),
            'icon'           => Request::string('icon', 'bi-stack'),
            'level'          => Request::string('level', 'beginner'),
            'mode'           => Request::string('mode', 'online'),
            'duration'       => Request::string('duration'),
            'schedule'       => Request::string('schedule'),
            'price'          => (float) Request::string('price', '0'),
            'original_price' => Request::string('original_price') !== '' ? (float) Request::string('original_price') : null,
            'total_seats'    => Request::int('total_seats'),
            'seats_left'     => Request::int('seats_left'),
            'highlights'     => Request::string('highlights'),
            'start_date'     => Request::string('start_date') ?: null,
            'end_date'       => Request::string('end_date') ?: null,
            'is_featured'    => Request::int('is_featured'),
            'status'         => Request::string('status', 'draft'),
        ];
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 1;
        while (true) {
            $sql = 'SELECT COUNT(*) FROM programs WHERE slug = ?';
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
