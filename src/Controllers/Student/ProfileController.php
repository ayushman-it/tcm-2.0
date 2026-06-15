<?php

declare(strict_types=1);

namespace TCM\Controllers\Student;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Core\Upload;
use TCM\Models\Order;

final class ProfileController extends Controller
{
    public function edit(): void
    {
        $user    = Auth::require('student');
        $profile = Database::first('SELECT * FROM student_profiles WHERE user_id = ?', [$user['id']]) ?? [];
        $this->view('student/profile', [
            'title'   => 'My Profile',
            'user'    => $user,
            'profile' => $profile,
            'orders'  => Order::forUser((int) $user['id']),
        ], 'student');
    }

    public function update(): void
    {
        $user = Auth::require('student');
        $this->validate([
            'name'         => 'required|min:2|max:150',
            'github_url'   => 'url',
            'linkedin_url' => 'url',
            'website_url'  => 'url',
        ], '/student/profile');

        $userUpdate = [
            'name'  => Request::string('name'),
            'phone' => Request::string('phone'),
        ];

        // Avatar upload
        if (Upload::present($_FILES['avatar'] ?? null)) {
            try {
                $uploadDir = config('uploads.path') . '/avatars';
                $filename  = Upload::store(
                    $_FILES['avatar'],
                    $uploadDir,
                    ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                    2 * 1024 * 1024
                );
                if (!empty($user['avatar'])) {
                    $old = config('uploads.path') . '/' . $user['avatar'];
                    if (is_file($old)) @unlink($old);
                }
                $userUpdate['avatar'] = 'avatars/' . $filename;
            } catch (\RuntimeException $e) {
                flash('error', $e->getMessage());
                redirect('/student/profile');
            }
        }

        Database::update('users', $userUpdate, ['id' => $user['id']]);

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
            'website_url'      => Request::string('website_url'),
            'twitter_url'      => Request::string('twitter_url'),
        ], ['user_id' => $user['id']]);

        $this->respond(null, 'Profile updated.', '/student/profile');
    }

    public function changePassword(): void
    {
        $user = Auth::require('student');
        $this->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ], '/student/profile');

        if (!password_verify(Request::string('current_password'), $user['password_hash'])) {
            flash('error', 'Your current password is incorrect.');
            redirect('/student/profile');
        }

        Database::update('users', [
            'password_hash' => password_hash(Request::string('password'), PASSWORD_DEFAULT),
        ], ['id' => $user['id']]);

        $this->respond(null, 'Password changed.', '/student/profile');
    }
}
