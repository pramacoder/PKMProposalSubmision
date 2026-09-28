<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pivot role_user — dibaca oleh User::roleRecords().
 * Tidak perlu timestamps default Eloquent karena tabel memakai timestamps().
 */
class RoleUser extends Model
{
    protected $table = 'role_user';

    protected $fillable = ['user_id', 'role'];
}
