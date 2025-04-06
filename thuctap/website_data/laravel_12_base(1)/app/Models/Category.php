<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'category'; 
    protected $primaryKey = 'id_cate';
    public $timestamps = false;

    const CREATED_AT = 'created_at_cate';
    const UPDATED_AT = 'update_at_cate';

    protected $fillable = [
        'title_cate', 'link_cate','status'
    ];
    public function posts()
    {
        return $this->hasMany(Post::class, 'id_cate', 'id_cate');
    }
}