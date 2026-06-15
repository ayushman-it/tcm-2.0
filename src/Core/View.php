<?php

declare(strict_types=1);

namespace TCM\Core;

/**
 * Minimal PHP template renderer with layout support.
 */
final class View
{
    private static string $viewPath = '';

    private static function path(): string
    {
        if (self::$viewPath === '') {
            self::$viewPath = dirname(__DIR__, 2) . '/views';
        }
        return self::$viewPath;
    }

    /**
     * Render a view into an optional layout.
     *
     * @param string               $view   dot or slash path under /views, e.g. "admin/courses/index"
     * @param array<string,mixed>  $data
     * @param string|null          $layout layout name under /views/layouts, or null for none
     */
    public static function render(string $view, array $data = [], ?string $layout = 'app'): void
    {
        $content = self::capture($view, $data);

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = self::path() . '/layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            echo $content;
            return;
        }

        $data['content'] = $content;
        (static function () use ($layoutFile, $data): void {
            extract($data, EXTR_SKIP);
            require $layoutFile;
        })();
    }

    /**
     * Render a view to a string.
     *
     * @param array<string,mixed> $data
     */
    public static function capture(string $view, array $data = []): string
    {
        $file = self::path() . '/' . str_replace('.', '/', $view) . '.php';
        if (!is_file($file)) {
            return "<!-- view not found: {$view} -->";
        }
        ob_start();
        (static function () use ($file, $data): void {
            extract($data, EXTR_SKIP);
            require $file;
        })();
        return (string) ob_get_clean();
    }

    /**
     * Render a partial in place (no layout).
     *
     * @param array<string,mixed> $data
     */
    public static function partial(string $view, array $data = []): void
    {
        echo self::capture($view, $data);
    }
}
