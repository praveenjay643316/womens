<?php

namespace App\Core;

/**
 * Tiny template engine: plain PHP view files plus an optional
 * section()/endSection()/extend() mechanism for Laravel-style layouts.
 *
 * Usage inside a view:
 *
 *   <?php View::section('content'); ?>
 *   ...markup...
 *   <?php View::endSection(); ?>
 *   <?php View::extend('layouts.app', ['title' => 'Users']); ?>
 *
 * Inside the layout:
 *
 *   <?= View::yieldSection('content') ?>
 */
class View
{
    protected static array $sections = [];
    protected static ?string $currentSection = null;

    /**
     * Render a view file (dot notation, e.g. "users.index") with the
     * given data and return the resulting HTML as a string.
     */
    public static function render(string $view, array $data = []): string
    {
        $viewPath = self::resolvePath($view);

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View [{$view}] not found at {$viewPath}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewPath;

        return ob_get_clean();
    }

    public static function resolvePath(string $view): string
    {
        return VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
    }

    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection(): void
    {
        if (self::$currentSection !== null) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    public static function yieldSection(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }

    /**
     * Render a layout and echo it immediately. Called from within a child
     * view after its section(s) have been captured.
     */
    public static function extend(string $layout, array $data = []): void
    {
        echo self::render($layout, $data);
    }
}
