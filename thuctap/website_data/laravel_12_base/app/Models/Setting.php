<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'key_name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key_name', 'value'];

    public static function setValue($key, $value)
    {
        self::updateOrCreate(
            ['key_name' => $key],
            ['value' => $value]
        );
    }

    public static function getValue($key, $default = null)
    {
        return optional(self::find($key))->value ?? $default;
    }
}
