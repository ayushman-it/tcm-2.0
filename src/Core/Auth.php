<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Session-based authentication for both admin and student users.
 */
final class Auth
{
    private const SESSION_KEY = '_auth_user_id';

    /**
     * Attempt to log a user in with email + password.
     *
     * @return array<string,mixed>|null The authenticated user row, or null on failure.
     */
    public static function attempt(string $email, string $password): ?array
    {
        $user = Database::first(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [strtolower(trim($email))]
        );

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        if ($user['status'] !== 'active') {
            return null;
        }

        // Transparently upgrade legacy hashes.
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            Database::update(
                'users',
                ['password_hash' => password_hash($password, PASSWORD_DEFAULT)],
                ['id' => $user['id']]
            );
        }

        self::login((int) $user['id']);
        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], ['id' => $user['id']]);

        return $user;
    }

    /**
     * Establish a session for the given user id (regenerates the session id).
     */
    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $userId;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public static function id(): ?int
    {
        return isset($_SESSION[self::SESSION_KEY]) ? (int) $_SESSION[self::SESSION_KEY] : null;
    }

    /**
     * Get the currently authenticated user, cached per-request.
     *
     * @return array<string,mixed>|null
     */
    public static function user(): ?array
    {
        static $cached = false;
        static $user = null;

        $id = self::id();
        if ($id === null) {
            return null;
        }
        if ($cached === false) {
            $user = Database::first('SELECT * FROM users WHERE id = ? LIMIT 1', [$id]);
            $cached = true;
        }
        return $user;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && $user['role'] === 'admin';
    }

    public static function isStudent(): bool
    {
        $user = self::user();
        return $user !== null && $user['role'] === 'student';
    }

    /**
     * Require an authenticated user with an optional role, redirecting otherwise.
     */
    public static function require(?string $role = null): array
    {
        $user = self::user();
        if ($user === null) {
            flash('error', 'Please sign in to continue.');
            redirect('/auth/login');
        }
        if ($role !== null && $user['role'] !== $role) {
            http_response_code(403);
            echo 'Forbidden: you do not have access to this area.';
            exit;
        }
        return $user;
    }

    /**
     * Register a new student account and return the new user id.
     */
    public static function registerStudent(string $name, string $email, string $password): int
    {
        $userId = Database::insert('users', [
            'name'          => trim($name),
            'email'         => strtolower(trim($email)),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => 'student',
            'status'        => 'active',
        ]);

        // Create an empty profile row so onboarding can update it.
        Database::insert('student_profiles', ['user_id' => $userId]);

        return $userId;
    }
}
