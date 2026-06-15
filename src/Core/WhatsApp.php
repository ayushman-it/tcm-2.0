<?php

declare(strict_types=1);

namespace TCM\Core;

use TCM\Models\Setting;

/**
 * Builds WhatsApp "click to chat" (wa.me) links so a visitor's WhatsApp opens
 * a chat to the business number with a pre-filled message — no paid API needed.
 */
final class WhatsApp
{
    /**
     * The business WhatsApp number in international format, digits only.
     */
    public static function number(): string
    {
        return preg_replace('/\D+/', '', Setting::get('whatsapp_number', '')) ?? '';
    }

    public static function isConfigured(): bool
    {
        return self::number() !== '';
    }

    /**
     * Build a wa.me link to the business number with the given message.
     */
    public static function link(string $message, ?string $number = null): string
    {
        $number = $number !== null ? (preg_replace('/\D+/', '', $number) ?? '') : self::number();
        $base = $number !== '' ? "https://wa.me/{$number}" : 'https://wa.me/';
        return $base . '?text=' . rawurlencode($message);
    }

    /**
     * Compose a standard enquiry message for a course / event / program.
     */
    public static function enquiryMessage(string $itemTitle, ?string $name = null, ?string $type = null): string
    {
        $intro = Setting::get('whatsapp_message', 'Hi The Code Munk! I am interested in');
        $label = $type !== null ? ucfirst(str_replace('_', ' ', $type)) . ': ' : '';
        $msg = trim($intro) . ' the ' . $label . '"' . $itemTitle . '".';
        if ($name !== null && $name !== '' && $name !== 'Guest') {
            $msg .= "\n\nMy name is {$name}.";
        }
        $msg .= "\nPlease share the details, fees and next batch.";
        return $msg;
    }
}
