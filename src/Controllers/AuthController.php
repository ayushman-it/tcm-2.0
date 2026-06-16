<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Mailer;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Models\Otp;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }
        $this->view('auth/login', ['title' => 'Sign In'], null);
    }

    public function login(): void
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], '/auth/login');

        $user = Auth::attempt(Request::string('email'), Request::string('password'));

        if ($user === null) {
            if (Request::isJson()) {
                \TCM\Core\Response::error('Invalid email or password.', 401);
            }
            flash('error', 'Invalid email or password.');
            $_SESSION['_old'] = ['email' => Request::string('email')];
            redirect('/auth/login');
        }

        if (Request::isJson()) {
            \TCM\Core\Response::success([
                'redirect' => $user['role'] === 'admin'
                    ? base_url('/admin')
                    : ((int) $user['onboarded'] === 0 ? base_url('/student/onboarding') : base_url('/student')),
                'name' => $user['name'],
            ], 'Welcome back, ' . $user['name'] . '!');
        }

        flash('success', 'Welcome back, ' . $user['name'] . '!');
        $this->redirectByRole();
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }
        $this->view('auth/register', ['title' => 'Create Account'], 'auth');
    }

    public function register(): void
    {
        $this->validate([
            'name'     => 'required|min:2|max:150',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], '/auth/register');

        $userId = Auth::registerStudent(
            Request::string('name'),
            Request::string('email'),
            Request::string('password')
        );

        Auth::login($userId);

        $this->sendWelcome(Request::string('name'), strtolower(Request::string('email')));

        if (Request::isJson()) {
            \TCM\Core\Response::success(['redirect' => base_url('/student/onboarding')], 'Account created.');
        }

        flash('success', 'Account created. Let\'s set up your profile.');
        redirect('/student/onboarding');
    }

    public function showOnboarding(): void
    {
        $user = Auth::require('student');
        $profile = Database::first('SELECT * FROM student_profiles WHERE user_id = ?', [$user['id']]) ?? [];
        $this->view('student/onboarding', [
            'title'   => 'Complete Your Profile',
            'user'    => $user,
            'profile' => $profile,
        ], null);
    }

    public function saveOnboarding(): void
    {
        $user = Auth::require('student');
        $this->validate([
            'headline'         => 'max:160',
            'experience_level' => 'in:beginner,intermediate,advanced',
            'github_url'       => 'url',
            'linkedin_url'     => 'url',
        ], '/student/onboarding');

        Database::update('student_profiles', [
            'headline'         => Request::string('headline'),
            'bio'              => Request::string('bio'),
            'location'         => Request::string('location'),
            'college'          => Request::string('college'),
            'graduation_year'  => Request::int('graduation_year') ?: null,
            'experience_level' => Request::string('experience_level', 'beginner'),
            'goal'             => Request::string('goal'),
            'github_url'       => Request::string('github_url'),
            'linkedin_url'     => Request::string('linkedin_url'),
        ], ['user_id' => $user['id']]);

        Database::update('users', ['onboarded' => 1], ['id' => $user['id']]);

        flash('success', 'Profile saved. Welcome to The Code Munk!');
        redirect('/student');
    }

    public function logout(): void
    {
        Auth::logout();
        flash('success', 'You have been signed out.');
        redirect('/auth/login');
    }

    // ----------------------------------------------------------------- //
    // Email OTP (passwordless) login — JSON, used by the site auth modal
    // ----------------------------------------------------------------- //
    public function otpRequest(): void
    {
        $email = strtolower(Request::string('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Please enter a valid email address.', 422);
        }

        $code = Otp::issue($email, 'login');
        $sent = Mailer::send(
            $email,
            'Your Code Munk login code: ' . $code,
            Mailer::template('Your login code', sprintf(
                '<p>Use this one-time code to sign in:</p>'
                . '<p style="font-size:30px;font-weight:800;letter-spacing:6px;color:#6c5ce7;">%s</p>'
                . '<p>The code expires in 10 minutes. If you did not request it, you can ignore this email.</p>',
                $code
            ))
        );

        if (!$sent && !Mailer::isConfigured()) {
            Response::error('Email is not configured on the server yet.', 500);
        }
        // Do not reveal whether the email maps to an account.
        Response::success(null, 'We have sent a 6-digit code to your email.');
    }

    public function otpVerify(): void
    {
        $email = strtolower(Request::string('email'));
        $code = Request::string('otp') ?: Request::string('code');

        if (!Otp::verify($email, $code, 'login')) {
            Response::error('Invalid or expired code. Please try again.', 401);
        }

        $user = Database::first('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
        $isNew = false;
        if ($user === null) {
            // Passwordless sign-up: create a verified student account.
            $userId = Auth::registerStudent(
                ucfirst(explode('@', $email)[0]),
                $email,
                bin2hex(random_bytes(12))
            );
            Database::update('users', ['email_verified' => 1], ['id' => $userId]);
            $user = Database::first('SELECT * FROM users WHERE id = ?', [$userId]);
            $isNew = true;
            $this->sendWelcome($user['name'], $email);
        } else {
            if ($user['status'] !== 'active') {
                Response::error('Your account is not active. Please contact support.', 403);
            }
            Database::update('users', ['email_verified' => 1, 'last_login_at' => date('Y-m-d H:i:s')], ['id' => $user['id']]);
        }

        Auth::login((int) $user['id']);
        Response::success(['redirect' => $this->dashboardUrl($user, $isNew)], 'Signed in successfully.');
    }

    // ----------------------------------------------------------------- //
    // Forgot / reset password via email OTP — JSON
    // ----------------------------------------------------------------- //
    public function passwordOtpRequest(): void
    {
        $email = strtolower(Request::string('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Please enter a valid email address.', 422);
        }

        $user = Database::first('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
        if ($user !== null) {
            $code = Otp::issue($email, 'reset');
            Mailer::send(
                $email,
                'Reset your Code Munk password: ' . $code,
                Mailer::template('Password reset code', sprintf(
                    '<p>Use this code to reset your password:</p>'
                    . '<p style="font-size:30px;font-weight:800;letter-spacing:6px;color:#6c5ce7;">%s</p>'
                    . '<p>The code expires in 10 minutes.</p>',
                    $code
                ))
            );
        }
        // Always respond the same way.
        Response::success(null, 'If that email has an account, a reset code is on its way.');
    }

    public function passwordReset(): void
    {
        $email = strtolower(Request::string('email'));
        $code = Request::string('otp') ?: Request::string('code');
        $password = Request::string('password');

        if (strlen($password) < 8) {
            Response::error('Password must be at least 8 characters.', 422);
        }
        if (!Otp::verify($email, $code, 'reset')) {
            Response::error('Invalid or expired code.', 401);
        }
        $user = Database::first('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
        if ($user === null) {
            Response::error('Account not found.', 404);
        }

        Database::update('users', [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ], ['id' => $user['id']]);

        Auth::login((int) $user['id']);
        Response::success(['redirect' => $this->dashboardUrl($user, false)], 'Password updated. You are signed in.');
    }

    private function dashboardUrl(array $user, bool $isNew): string
    {
        if ($user['role'] === 'admin') {
            return base_url('/admin');
        }
        return ($isNew || (int) $user['onboarded'] === 0) ? base_url('/student/onboarding') : base_url('/student');
    }

    private function sendWelcome(string $name, string $email): void
    {
        Mailer::send(
            $email,
            'Welcome to The Code Munk 🎉',
            Mailer::template('Welcome, ' . $name . '!', sprintf(
                '<p>Your account is ready. You can explore courses, join live programs, '
                . 'apply for internships and build your developer portfolio.</p>'
                . '<p><a href="%s" style="display:inline-block;background:#6c5ce7;color:#fff;padding:11px 20px;border-radius:10px;text-decoration:none;font-weight:600;">Go to your dashboard</a></p>',
                base_url('/student')
            ))
        );
    }

    private function redirectByRole(): never
    {
        if (Auth::isAdmin()) {
            redirect('/admin');
        }
        $user = Auth::user();
        if ($user !== null && (int) $user['onboarded'] === 0) {
            redirect('/student/onboarding');
        }
        redirect('/student');
    }
}
