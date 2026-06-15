<?php

declare(strict_types=1);

namespace TCM\Controllers;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirectByRole();
        }
        $this->view('auth/login', ['title' => 'Sign In'], 'auth');
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
        ], 'auth');
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
