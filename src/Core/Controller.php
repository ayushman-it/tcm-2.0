<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Base controller with shared rendering and validation helpers.
 */
abstract class Controller
{
    /**
     * Render a view within a layout.
     *
     * @param array<string,mixed> $data
     */
    protected function view(string $view, array $data = [], ?string $layout = 'app'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Validate request data; on failure flash errors + old input and redirect back.
     *
     * @param array<string,string> $rules
     * @return array<string,mixed> validated input
     */
    protected function validate(array $rules, string $redirectBack): array
    {
        $data = Request::all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            if (Request::isJson()) {
                Response::error('Validation failed.', 422, $validator->errors());
            }
            $_SESSION['_old'] = $data;
            $_SESSION['_errors'] = $validator->errors();
            flash('error', $validator->first() ?? 'Please fix the errors and try again.');
            redirect($redirectBack);
        }

        unset($_SESSION['_old'], $_SESSION['_errors']);
        return $data;
    }

    /**
     * Respond appropriately for JSON or web clients.
     *
     * @param mixed $data
     */
    protected function respond(mixed $data, string $message, string $redirectTo): void
    {
        if (Request::isJson()) {
            Response::success($data, $message);
        }
        flash('success', $message);
        redirect($redirectTo);
    }
}
