<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'user_id',
        'rating',
        'review',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

     public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
