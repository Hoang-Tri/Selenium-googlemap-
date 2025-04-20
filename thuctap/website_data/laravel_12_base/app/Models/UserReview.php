<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    use HasFactory;

    protected $table = 'users_review'; // Bảng cần làm việc
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['location_id', 'user_review','star', 'creat_date','data_llm'];

    // Định nghĩa mối quan hệ với bảng Location (khóa ngoại)
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id'); 
    }
}
