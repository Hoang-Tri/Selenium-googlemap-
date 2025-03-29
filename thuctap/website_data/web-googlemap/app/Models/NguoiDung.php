<?php

namespace App\Models; // Đúng namespace

use Illuminate\Database\Eloquent\Model;

class NguoiDung extends Model
{
    protected $table = 'users'; // Laravel mặc định là 'users', kiểm tra lại DB

    protected $primaryKey = 'id'; // Chữ 'K' phải viết hoa

    protected $fillable = ['email', 'password', 'name'];    
}