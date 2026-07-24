<?php

use App\Core\View;

View::section('content'); ?>

<div class="page-header">
    <h1>Users</h1>
    <a href="/users/create" class="btn btn-primary">+ New User</a>
</div>

<?php if (empty($users)): ?>
    <div class="card">
        <p class="empty-state">No users found. Create your first user to get started.</p>
    </div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['id']) ?></td>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['created_at']) ?></td>
                    <td class="actions">
                        <a href="/users/<?= e($user['id']) ?>/edit" class="btn btn-sm">Edit</a>
                        <form class="inline-form" action="/users/<?= e($user['id']) ?>" method="POST"
                              onsubmit="return confirm('Delete this user?');">
                            <?= method_field('DELETE') ?>
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php View::endSection(); ?>
<?php View::extend('layouts.app', ['title' => 'Users']); ?>
