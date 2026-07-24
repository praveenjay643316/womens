<?php

use App\Core\View;

View::section('content'); ?>

<div class="page-header">
    <h1>Edit User</h1>
    <a href="/users" class="btn">&larr; Back to Users</a>
</div>

<div class="card">
    <form action="/users/<?= e($user['id']) ?>" method="POST">
        <?= method_field('PUT') ?>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control"
                   value="<?= e(old('name', $user['name'])) ?>">
            <?php foreach (errors('name') as $message): ?>
                <div class="field-error"><?= e($message) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="<?= e(old('email', $user['email'])) ?>">
            <?php foreach (errors('email') as $message): ?>
                <div class="field-error"><?= e($message) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="/users" class="btn">Cancel</a>
        </div>
    </form>
</div>

<?php View::endSection(); ?>
<?php View::extend('layouts.app', ['title' => 'Edit User']); ?>
