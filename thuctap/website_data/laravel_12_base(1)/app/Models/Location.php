<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    protected $table = 'locations'; 
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name', 'address','data_llm'
    ];
}