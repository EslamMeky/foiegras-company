<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
     protected $fillable=[
        'id',
        'user_id',
        'product_id',
        'rating',
        'review',
        'created_at',
        'updated_at',
    ];

    public $timestamps=true;

    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function products(){
        return $this->belongsTo(Product::class,'product_id');
    }

}
