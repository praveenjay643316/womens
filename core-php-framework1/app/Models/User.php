<?php

namespace App\Models;

use App\Core\Model;

/**
 * Users table model.
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
}
