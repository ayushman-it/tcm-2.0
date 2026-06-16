<?php

declare(strict_types=1);

/**
 * The Code Munk - Front Controller
 *
 * The public marketing site is served as static HTML (the original design).
 * This controller powers the dynamic application: auth, admin & student
 * dashboards, the JSON API, lead/WhatsApp capture and public portfolios.
 */

use TCM\Core\Csrf;
use TCM\Core\Request;
use TCM\Core\Response;
use TCM\Core\Router;
use TCM\Core\View;

require __DIR__ . '/src/bootstrap.php';

// CSRF protection for state-changing web requests.
// JSON/AJAX requests from the frontend modal are exempt (same-origin fetch).
if (!str_starts_with(Request::path(), '/api') && !Request::isJson()) {
    Csrf::check();
}

$router = new Router();

$router->get('/health', static fn () => Response::success(['time' => date('c')], 'OK'));

// --------------------------------------------------------------------- //
// Authentication
// --------------------------------------------------------------------- //
$router->get('/auth/login', ['TCM\Controllers\AuthController', 'showLogin']);
$router->post('/auth/login', ['TCM\Controllers\AuthController', 'login']);
$router->get('/auth/register', ['TCM\Controllers\AuthController', 'showRegister']);
$router->post('/auth/register', ['TCM\Controllers\AuthController', 'register']);
$router->post('/auth/logout', ['TCM\Controllers\AuthController', 'logout']);
$router->post('/auth/otp/request', ['TCM\Controllers\AuthController', 'otpRequest']);
$router->post('/auth/otp/verify', ['TCM\Controllers\AuthController', 'otpVerify']);
$router->post('/auth/password/request', ['TCM\Controllers\AuthController', 'passwordOtpRequest']);
$router->post('/auth/password/reset', ['TCM\Controllers\AuthController', 'passwordReset']);

// Google OAuth
$router->get('/auth/google', ['TCM\Controllers\GoogleAuthController', 'redirect']);
$router->get('/auth/google/callback', ['TCM\Controllers\GoogleAuthController', 'callback']);

// --------------------------------------------------------------------- //
// Admin dashboard
// --------------------------------------------------------------------- //
$router->get('/admin', ['TCM\Controllers\Admin\DashboardController', 'index']);

$router->get('/admin/courses', ['TCM\Controllers\Admin\CourseController', 'index']);
$router->get('/admin/courses/create', ['TCM\Controllers\Admin\CourseController', 'create']);
$router->post('/admin/courses', ['TCM\Controllers\Admin\CourseController', 'store']);
$router->get('/admin/courses/{id}/edit', ['TCM\Controllers\Admin\CourseController', 'edit']);
$router->post('/admin/courses/{id}', ['TCM\Controllers\Admin\CourseController', 'update']);
$router->post('/admin/courses/{id}/delete', ['TCM\Controllers\Admin\CourseController', 'destroy']);
$router->post('/admin/courses/{id}/modules', ['TCM\Controllers\Admin\CourseController', 'addModule']);
$router->post('/admin/modules/{moduleId}/delete', ['TCM\Controllers\Admin\CourseController', 'deleteModule']);
$router->post('/admin/modules/{moduleId}/lessons', ['TCM\Controllers\Admin\CourseController', 'addLesson']);
$router->post('/admin/lessons/{lessonId}/delete', ['TCM\Controllers\Admin\CourseController', 'deleteLesson']);

$router->get('/admin/events', ['TCM\Controllers\Admin\EventController', 'index']);
$router->get('/admin/events/create', ['TCM\Controllers\Admin\EventController', 'create']);
$router->post('/admin/events', ['TCM\Controllers\Admin\EventController', 'store']);
$router->get('/admin/events/{id}/edit', ['TCM\Controllers\Admin\EventController', 'edit']);
$router->post('/admin/events/{id}', ['TCM\Controllers\Admin\EventController', 'update']);
$router->post('/admin/events/{id}/delete', ['TCM\Controllers\Admin\EventController', 'destroy']);

$router->get('/admin/programs', ['TCM\Controllers\Admin\ProgramController', 'index']);
$router->get('/admin/programs/create', ['TCM\Controllers\Admin\ProgramController', 'create']);
$router->post('/admin/programs', ['TCM\Controllers\Admin\ProgramController', 'store']);
$router->get('/admin/programs/{id}/edit', ['TCM\Controllers\Admin\ProgramController', 'edit']);
$router->post('/admin/programs/{id}', ['TCM\Controllers\Admin\ProgramController', 'update']);
$router->post('/admin/programs/{id}/delete', ['TCM\Controllers\Admin\ProgramController', 'destroy']);
$router->post('/admin/programs/{id}/sessions', ['TCM\Controllers\Admin\ProgramController', 'addSession']);
$router->post('/admin/sessions/{sessionId}', ['TCM\Controllers\Admin\ProgramController', 'updateSession']);
$router->post('/admin/sessions/{sessionId}/delete', ['TCM\Controllers\Admin\ProgramController', 'deleteSession']);

$router->get('/admin/internships', ['TCM\Controllers\Admin\InternshipController', 'index']);
$router->get('/admin/internships/{id}', ['TCM\Controllers\Admin\InternshipController', 'show']);
$router->post('/admin/internships/{id}', ['TCM\Controllers\Admin\InternshipController', 'update']);
$router->get('/admin/internships/{id}/resume', ['TCM\Controllers\Admin\InternshipController', 'downloadResume']);

$router->get('/admin/leads', ['TCM\Controllers\Admin\LeadController', 'index']);
$router->post('/admin/leads/{id}/status', ['TCM\Controllers\Admin\LeadController', 'updateStatus']);
$router->post('/admin/leads/{id}/convert', ['TCM\Controllers\Admin\LeadController', 'convert']);
$router->post('/admin/leads/{id}/delete', ['TCM\Controllers\Admin\LeadController', 'destroy']);

$router->get('/admin/posts', ['TCM\Controllers\Admin\PostController', 'index']);
$router->get('/admin/posts/create', ['TCM\Controllers\Admin\PostController', 'create']);
$router->post('/admin/posts', ['TCM\Controllers\Admin\PostController', 'store']);
$router->get('/admin/posts/{id}/edit', ['TCM\Controllers\Admin\PostController', 'edit']);
$router->post('/admin/posts/{id}', ['TCM\Controllers\Admin\PostController', 'update']);
$router->post('/admin/posts/{id}/delete', ['TCM\Controllers\Admin\PostController', 'destroy']);

$router->get('/admin/students', ['TCM\Controllers\Admin\StudentController', 'index']);
$router->get('/admin/students/{id}', ['TCM\Controllers\Admin\StudentController', 'show']);
$router->post('/admin/students/{id}/toggle', ['TCM\Controllers\Admin\StudentController', 'toggleStatus']);

$router->get('/admin/categories', ['TCM\Controllers\Admin\ContentController', 'categories']);
$router->post('/admin/categories', ['TCM\Controllers\Admin\ContentController', 'storeCategory']);
$router->post('/admin/categories/{id}/delete', ['TCM\Controllers\Admin\ContentController', 'deleteCategory']);

$router->get('/admin/testimonials', ['TCM\Controllers\Admin\ContentController', 'testimonials']);
$router->post('/admin/testimonials', ['TCM\Controllers\Admin\ContentController', 'storeTestimonial']);
$router->post('/admin/testimonials/{id}/delete', ['TCM\Controllers\Admin\ContentController', 'deleteTestimonial']);

$router->get('/admin/messages', ['TCM\Controllers\Admin\ContentController', 'messages']);
$router->post('/admin/messages/{id}/delete', ['TCM\Controllers\Admin\ContentController', 'deleteMessage']);

$router->get('/admin/settings', ['TCM\Controllers\Admin\ContentController', 'settings']);
$router->post('/admin/settings', ['TCM\Controllers\Admin\ContentController', 'saveSettings']);

// Lead Forms
$router->get('/admin/lead-forms', ['TCM\Controllers\Admin\LeadFormController', 'index']);
$router->get('/admin/lead-forms/create', ['TCM\Controllers\Admin\LeadFormController', 'create']);
$router->post('/admin/lead-forms', ['TCM\Controllers\Admin\LeadFormController', 'store']);
$router->get('/admin/lead-forms/{id}/edit', ['TCM\Controllers\Admin\LeadFormController', 'edit']);
$router->post('/admin/lead-forms/{id}', ['TCM\Controllers\Admin\LeadFormController', 'update']);
$router->post('/admin/lead-forms/{id}/delete', ['TCM\Controllers\Admin\LeadFormController', 'destroy']);

// Public lead form pages
$router->get('/form/{slug}', ['TCM\Controllers\Admin\LeadFormController', 'show']);
$router->post('/form/{slug}/submit', ['TCM\Controllers\Admin\LeadFormController', 'submit']);
$router->get('/form/{slug}/thank-you', ['TCM\Controllers\Admin\LeadFormController', 'thankYou']);

// --------------------------------------------------------------------- //
// Student dashboard
// --------------------------------------------------------------------- //
$router->get('/student', ['TCM\Controllers\Student\DashboardController', 'index']);
$router->get('/student/onboarding', ['TCM\Controllers\AuthController', 'showOnboarding']);
$router->post('/student/onboarding', ['TCM\Controllers\AuthController', 'saveOnboarding']);

$router->get('/student/profile', ['TCM\Controllers\Student\ProfileController', 'edit']);
$router->post('/student/profile', ['TCM\Controllers\Student\ProfileController', 'update']);
$router->post('/student/profile/password', ['TCM\Controllers\Student\ProfileController', 'changePassword']);

$router->get('/student/courses', ['TCM\Controllers\Student\CourseController', 'browse']);
$router->get('/student/courses/{slug}', ['TCM\Controllers\Student\CourseController', 'show']);
$router->post('/student/courses/{id}/buy', ['TCM\Controllers\Student\CourseController', 'purchase']);
$router->get('/student/learn/{id}', ['TCM\Controllers\Student\CourseController', 'learn']);
$router->post('/student/learn/{id}/lessons/{lessonId}', ['TCM\Controllers\Student\CourseController', 'toggleLesson']);

$router->get('/student/events', ['TCM\Controllers\Student\EventController', 'browse']);
$router->post('/student/events/{id}/join', ['TCM\Controllers\Student\EventController', 'join']);

$router->get('/student/programs', ['TCM\Controllers\Student\ProgramController', 'browse']);
$router->get('/student/programs/{slug}', ['TCM\Controllers\Student\ProgramController', 'show']);
$router->post('/student/programs/{id}/enquire', ['TCM\Controllers\Student\ProgramController', 'enquire']);

$router->get('/student/applications', ['TCM\Controllers\Student\InternshipController', 'applications']);
$router->get('/student/internships/{id}/apply', ['TCM\Controllers\Student\InternshipController', 'showForm']);
$router->post('/student/internships/{id}/apply', ['TCM\Controllers\Student\InternshipController', 'apply']);

$router->get('/student/portfolio', ['TCM\Controllers\Student\PortfolioController', 'index']);
$router->post('/student/portfolio/projects', ['TCM\Controllers\Student\PortfolioController', 'storeProject']);
$router->post('/student/portfolio/projects/{id}/delete', ['TCM\Controllers\Student\PortfolioController', 'deleteProject']);
$router->post('/student/portfolio/skills', ['TCM\Controllers\Student\PortfolioController', 'storeSkill']);
$router->post('/student/portfolio/skills/{id}/delete', ['TCM\Controllers\Student\PortfolioController', 'deleteSkill']);
$router->post('/student/portfolio/achievements', ['TCM\Controllers\Student\PortfolioController', 'storeAchievement']);
$router->post('/student/portfolio/achievements/{id}/delete', ['TCM\Controllers\Student\PortfolioController', 'deleteAchievement']);

// Public, shareable portfolio
$router->get('/portfolio/{id}', ['TCM\Controllers\Student\PortfolioController', 'publicView']);

// Lead capture + WhatsApp hand-off (used by the static site CTAs and dashboards)
$router->post('/enquiry', ['TCM\Controllers\EnquiryController', 'store']);

// --------------------------------------------------------------------- //
// Public JSON API (consumed by the static marketing site via tcm-app.js)
// --------------------------------------------------------------------- //
$router->get('/api/me', ['TCM\Controllers\Api\PublicController', 'me']);
$router->get('/api/courses', ['TCM\Controllers\Api\PublicController', 'courses']);
$router->get('/api/courses/{slug}', ['TCM\Controllers\Api\PublicController', 'course']);
$router->get('/api/events', ['TCM\Controllers\Api\PublicController', 'events']);
$router->get('/api/programs', ['TCM\Controllers\Api\PublicController', 'programs']);
$router->get('/api/posts', ['TCM\Controllers\Api\PublicController', 'posts']);
$router->get('/api/testimonials', ['TCM\Controllers\Api\PublicController', 'testimonials']);
$router->post('/api/contact', ['TCM\Controllers\Api\PublicController', 'contact']);
$router->post('/api/subscribe', ['TCM\Controllers\Api\PublicController', 'subscribe']);

// --------------------------------------------------------------------- //
// Dispatch
// --------------------------------------------------------------------- //
try {
    $router->dispatch(Request::method(), Request::path());
} catch (\Throwable $e) {
    http_response_code(500);
    if (Request::isJson()) {
        Response::error(config('app.debug') ? $e->getMessage() : 'Server error.', 500);
    }
    if (config('app.debug')) {
        echo '<pre style="padding:2rem;font-family:monospace;">';
        echo 'Error: ' . e($e->getMessage()) . "\n\n" . e($e->getTraceAsString());
        echo '</pre>';
    } else {
        View::render('errors/500', [], 'public');
    }
}
