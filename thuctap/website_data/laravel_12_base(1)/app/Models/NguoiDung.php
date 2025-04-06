<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NguoiDung extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // Bảng trong database

    protected $fillable = [
         'email', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
