<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Core\Validator;
use TCM\Core\WhatsApp;
use TCM\Models\Lead;

/**
 * Central lead-capture + WhatsApp routing used across the public site and the
 * student dashboard. The platform never simulates payment: an enquiry is saved
 * and the visitor is handed off to WhatsApp (number-to-number) to finalise.
 */
final class EnquiryController extends Controller
{
    /**
     * Resolve a course/event/program title for the given interest.
     */
    private function resolveTitle(string $type, ?int $id): ?string
    {
        if ($id === null || $id <= 0) {
            return null;
        }
        $table = match ($type) {
            'course'  => 'courses',
            'event'   => 'events',
            'program' => 'programs',
            default   => null,
        };
        if ($table === null) {
            return null;
        }
        $row = Database::first("SELECT title FROM `$table` WHERE id = ?", [$id]);
        return $row['title'] ?? null;
    }

    /**
     * Handle an enquiry submission and redirect to WhatsApp.
     *
     * Works for guests (name + phone required) and logged-in students
     * (details pulled from their account).
     */
    public function store(): void
    {
        $user = Auth::user();
        $type = Request::string('interest_type', 'general');
        $id = Request::int('interest_id') ?: null;

        // Pull contact details from the account when signed in.
        if ($user !== null) {
            $name = $user['name'];
            $email = $user['email'];
            $phone = Request::string('phone') ?: ($user['phone'] ?? '');
        } else {
            $data = Request::all();
            $validator = Validator::make($data, [
                'name'  => 'required|min:2|max:150',
                'phone' => 'required|min:7|max:20',
                'email' => 'email',
            ]);
            if ($validator->fails()) {
                if (Request::isJson()) {
                    Response::error('Please provide your name and phone.', 422, $validator->errors());
                }
                $_SESSION['_old'] = $data;
                flash('error', $validator->first() ?? 'Please provide your name and phone number.');
                redirect(Request::string('return', '/'));
            }
            $name = Request::string('name');
            $email = Request::string('email') ?: null;
            $phone = Request::string('phone');
        }

        $title = $this->resolveTitle($type, $id) ?? Request::string('interest_title') ?: null;

        $leadId = Lead::capture([
            'user_id'        => $user['id'] ?? null,
            'name'           => $name,
            'email'          => $email,
            'phone'          => $phone,
            'interest_type'  => $type,
            'interest_id'    => $id,
            'interest_title' => $title,
            'message'        => Request::string('message') ?: null,
            'source'         => Request::string('source', 'website'),
        ]);

        $message = $title !== null
            ? WhatsApp::enquiryMessage($title, $name, $type)
            : trim((Request::string('message') ?: 'Hi The Code Munk! I would like to know more.'));

        $waLink = WhatsApp::link($message);
        Lead::update($leadId, ['whatsapp_sent' => 1]);

        if (Request::isJson()) {
            Response::success(['whatsapp_url' => $waLink, 'lead_id' => $leadId], 'Enquiry captured.');
        }

        // Hand off to WhatsApp (opens the visitor's WhatsApp to our number).
        redirect($waLink);
    }
}
