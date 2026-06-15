<?php

declare(strict_types=1);

namespace TCM\Core;

use RuntimeException;

/**
 * Safe file upload handling with extension/size validation and random naming.
 */
final class Upload
{
    /**
     * Validate and move an uploaded file into $destDir, returning the stored
     * filename. Throws RuntimeException with a user-friendly message on failure.
     *
     * @param array<string,mixed> $file    A single entry from $_FILES
     * @param list<string>        $allowed Allowed lowercase extensions
     */
    public static function store(array $file, string $destDir, array $allowed, ?int $maxSize = null): string
    {
        $maxSize ??= (int) config('uploads.max_size', 5 * 1024 * 1024);

        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid upload.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Please choose a file to upload.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('The file is too large.');
            default:
                throw new RuntimeException('Upload failed. Please try again.');
        }

        if (($file['size'] ?? 0) > $maxSize) {
            throw new RuntimeException('The file exceeds the ' . round($maxSize / 1048576) . 'MB limit.');
        }

        $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            throw new RuntimeException('Allowed file types: ' . implode(', ', $allowed) . '.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Invalid upload source.');
        }

        if (!is_dir($destDir) && !mkdir($destDir, 0775, true) && !is_dir($destDir)) {
            throw new RuntimeException('Could not create the upload directory.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $target = rtrim($destDir, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException('Could not save the uploaded file.');
        }

        return $filename;
    }

    /**
     * Whether a file input actually contains an uploaded file.
     *
     * @param array<string,mixed>|null $file
     */
    public static function present(?array $file): bool
    {
        return $file !== null
            && isset($file['error'])
            && $file['error'] !== UPLOAD_ERR_NO_FILE;
    }
}
