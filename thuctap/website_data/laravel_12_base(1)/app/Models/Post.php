<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post'; // Tên bảng

    protected $primaryKey = 'id_post'; // Khóa chính

    public $timestamps = false; // Nếu không có `created_at` và `updated_at`

    protected $fillable = ['title_post', 'id_cate', 'author', 'content_post', 'status_post'];

    // Quan hệ với bảng Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_cate', 'id_cate');
    }
}
