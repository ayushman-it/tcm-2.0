<?php

declare(strict_types=1);

namespace TCM\Controllers\Student;

use TCM\Core\Auth;
use TCM\Core\Controller;
use TCM\Core\Database;
use TCM\Core\Request;
use TCM\Models\Portfolio;

final class PortfolioController extends Controller
{
    public function index(): void
    {
        $user = Auth::require('student');
        $uid = (int) $user['id'];
        $profile = Database::first('SELECT * FROM student_profiles WHERE user_id = ?', [$uid]) ?? [];

        $this->view('student/portfolio/index', [
            'title'        => 'My Portfolio',
            'user'         => $user,
            'profile'      => $profile,
            'projects'     => Portfolio::projects($uid),
            'skills'       => Portfolio::skills($uid),
            'achievements' => Portfolio::achievements($uid),
            'certificates' => Portfolio::certificates($uid),
            'strength'     => Portfolio::strength($uid),
        ], 'student');
    }

    /**
     * Public, shareable portfolio page (no auth required).
     */
    public function publicView(array $params): void
    {
        $userId = (int) $params['id'];
        $user = Database::first("SELECT id, name, avatar FROM users WHERE id = ? AND role = 'student'", [$userId]);
        if ($user === null) {
            http_response_code(404);
            $this->view('errors/404', [], 'public');
            return;
        }
        $profile = Database::first('SELECT * FROM student_profiles WHERE user_id = ?', [$userId]) ?? [];

        $this->view('student/portfolio/public', [
            'title'        => $user['name'] . ' - Portfolio',
            'owner'        => $user,
            'profile'      => $profile,
            'projects'     => Portfolio::projects($userId),
            'skills'       => Portfolio::skills($userId),
            'achievements' => Portfolio::achievements($userId),
            'certificates' => Portfolio::certificates($userId),
        ], 'public');
    }

    // --- Projects --------------------------------------------------------
    public function storeProject(): void
    {
        $user = Auth::require('student');
        $this->validate([
            'title'    => 'required|max:180',
            'repo_url' => 'url',
            'live_url' => 'url',
        ], '/student/portfolio');

        Database::insert('portfolio_projects', [
            'user_id'     => (int) $user['id'],
            'title'       => Request::string('title'),
            'description' => Request::string('description'),
            'tech_stack'  => Request::string('tech_stack'),
            'repo_url'    => Request::string('repo_url'),
            'live_url'    => Request::string('live_url'),
            'is_featured' => Request::int('is_featured'),
        ]);
        $this->respond(null, 'Project added to your portfolio.', '/student/portfolio');
    }

    public function deleteProject(array $params): void
    {
        $user = Auth::require('student');
        Database::delete('portfolio_projects', [
            'id'      => (int) $params['id'],
            'user_id' => (int) $user['id'],
        ]);
        $this->respond(null, 'Project removed.', '/student/portfolio');
    }

    // --- Skills ----------------------------------------------------------
    public function storeSkill(): void
    {
        $user = Auth::require('student');
        $this->validate(['name' => 'required|max:80'], '/student/portfolio');
        Database::insert('portfolio_skills', [
            'user_id' => (int) $user['id'],
            'name'    => Request::string('name'),
            'level'   => min(100, max(0, Request::int('level', 50))),
        ]);
        $this->respond(null, 'Skill added.', '/student/portfolio');
    }

    public function deleteSkill(array $params): void
    {
        $user = Auth::require('student');
        Database::delete('portfolio_skills', [
            'id'      => (int) $params['id'],
            'user_id' => (int) $user['id'],
        ]);
        $this->respond(null, 'Skill removed.', '/student/portfolio');
    }

    // --- Achievements ----------------------------------------------------
    public function storeAchievement(): void
    {
        $user = Auth::require('student');
        $this->validate(['title' => 'required|max:180', 'url' => 'url'], '/student/portfolio');
        Database::insert('portfolio_achievements', [
            'user_id'     => (int) $user['id'],
            'title'       => Request::string('title'),
            'issuer'      => Request::string('issuer'),
            'description' => Request::string('description'),
            'url'         => Request::string('url'),
            'achieved_on' => Request::string('achieved_on') ?: null,
        ]);
        $this->respond(null, 'Achievement added.', '/student/portfolio');
    }

    public function deleteAchievement(array $params): void
    {
        $user = Auth::require('student');
        Database::delete('portfolio_achievements', [
            'id'      => (int) $params['id'],
            'user_id' => (int) $user['id'],
        ]);
        $this->respond(null, 'Achievement removed.', '/student/portfolio');
    }
}
