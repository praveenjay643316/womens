# Core PHP Framework

A small, dependency-free MVC framework built from scratch in plain PHP
8.2+ — no Laravel, Symfony, CodeIgniter, or Slim. It borrows familiar
Laravel-style ergonomics (`view()`, `$request->validate()`, route
parameters, PSR-4 autoloading) while staying 100% Core PHP under the hood.

## Requirements

- PHP 8.2+
- PDO extension with the `pdo_pgsql` driver
- Composer
- PostgreSQL (config defaults to `pgsql`; `App\Core\Database` also supports
  `mysql` if you ever need to switch — set `DB_CONNECTION=mysql` in `.env`)

## Getting Started

```bash
composer install
```

This generates `vendor/autoload.php` from the PSR-4 mapping in
`composer.json`. **No packages are downloaded** — the framework has zero
third-party dependencies (see "Why no dependencies?" below) — so this
works fully offline.

Create your database and run the migration:

```bash
createdb core_php_framework
psql -U postgres -d core_php_framework -f database/migrations/001_create_users_table.sql
```

Copy `.env.example` to `.env` (already done for you) and update the
`DB_*` values to match your database:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=core_php_framework
DB_USERNAME=postgres
DB_PASSWORD=
```

Start the built-in dev server:

```bash
composer serve
```

This runs `php -S localhost:8000 -t public` — no Apache/Nginx config
needed. Visit `http://localhost:8000/users`.

## Folder Structure

```
app/
├── Controllers/     Application controllers (UserController, ...)
├── Models/          Active-Record-style models extending Core\Model
├── Services/        Optional: business logic that spans multiple models
├── Repositories/     Optional: dedicated query layer
├── Requests/        Optional: dedicated Form Request classes
├── Middleware/       Route middleware (VerifyCsrfToken, ...)
├── Helpers/          helpers.php - global functions (view, redirect, dd, ...)
└── Core/             The framework itself (Router, Model, Database, ...)

bootstrap/app.php     Loads env, config, routes; returns an App instance
config/                app.php, database.php
database/migrations/   Plain .sql migration files
public/                Web root: index.php (front controller) + assets/
resources/views/       Plain-PHP views + errors/
routes/web.php          Route definitions
storage/logs/           app.log (write errors here)
storage/uploads/        File upload destination
```

## Routing

```php
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
$router->put('/users/{id}', [UserController::class, 'update'], [VerifyCsrfToken::class]);
$router->delete('/users/{id}', [UserController::class, 'destroy'], [VerifyCsrfToken::class]);
```

PUT/DELETE are reached from HTML forms via method spoofing — render
`<?= method_field('PUT') ?>` inside a POST `<form>`.

Middleware is optional per-route (third argument) and follows a
Laravel-style pipeline: `handle(Request $request, callable $next)`.

## Views & Layouts

```php
// In a controller
return $this->view('users.index', ['users' => $users]);
```

```php
<!-- resources/views/users/index.php -->
<?php
use App\Core\View;
View::section('content'); ?>
    <h1>Users</h1>
<?php View::endSection(); ?>
<?php View::extend('layouts.app', ['title' => 'Users']); ?>
```

```php
<!-- resources/views/layouts/app.php -->
<?= \App\Core\View::yieldSection('content') ?>
```

## Validation

```php
$data = $request->validate([
    'name'  => 'required|max:100',
    'email' => 'required|email|unique:users,email',
]);
```

Supported rules: `required`, `email`, `min:n`, `max:n`, `numeric`,
`confirmed`, `unique:table,column,ignoreId`. On failure the browser is
redirected back with errors + old input flashed to the session for one
request — read them in views with `old('field')` and `errors('field')`.

## Security

- **Prepared statements** everywhere (PDO, `ATTR_EMULATE_PREPARES` off)
- **CSRF protection** via `VerifyCsrfToken` middleware + `csrf_field()` helper
- **XSS escaping** via the `e()` helper — use it around all user data in views
- **Password hashing**: use PHP's built-in `password_hash()` / `password_verify()`
  when you add authentication (not included in the base User CRUD, which
  has no password field)
- **Sessions**: started centrally in `App::run()` via `Core\Session`

## Global Helpers

`view()`, `redirect()`, `dd()`, `config()`, `env()`, `old()`, `errors()`,
`e()`, `csrf_token()`, `csrf_field()`, `method_field()`, `asset()`, `url()`

## Why no dependencies?

The brief calls for a Core PHP framework, so the `.env` parser
(`App\Core\Env`) and everything else here is hand-written rather than
pulling in `vlucas/phpdotenv` or similar. The upside beyond philosophy:
`composer install` never touches Packagist, so the project installs and
runs fully offline.

## Extending

- **Requests/**, **Repositories/**, **Services/** are scaffolded but
  empty — each has a short README explaining when you'd reach for it.
  The included User CRUD keeps validation inline in the controller,
  matching the `$request->validate([...])` pattern shown above; extract
  to `Requests/` once rules grow.
- **Model** gives you `all()`, `find()`, `where()`, `create()`, `update()`,
  `delete()`, `count()`. For anything more complex (joins, pagination),
  either add methods to a specific model or drop to
  `static::query()` for raw PDO.
