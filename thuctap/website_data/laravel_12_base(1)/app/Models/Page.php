<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $table = 'page'; 
    protected $primaryKey = 'id_page';
    public $timestamps = true; 
    const CREATED_AT = 'created_at_page'; 
    const UPDATED_AT = 'updated_at_page'; 
    protected $fillable = ['title_page', 'slug_page', 'author_page', 'content_page', 'status_page'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug_page)) {
                $page->slug_page = convertToSlug($page->title_page);
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title_page')) {  
                $page->slug_page = convertToSlug($page->title_page);
            }
        });
    }
}