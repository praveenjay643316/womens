<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name')) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="/" class="brand"><?= e(config('app.name')) ?></a>
            <a href="/users">Users</a>
        </div>
    </nav>

    <main class="container">
        <?= \App\Core\View::yieldSection('content') ?>
    </main>
</body>
</html>
