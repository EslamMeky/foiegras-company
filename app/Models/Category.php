<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable=[
        'id',
        'title_ar',
        'title_en',
        'desc_ar',
        'desc_en',
        'image',
        'features_ids',
        'popular_products_ids',
        'created_at',
        'updated_at'
    ];
     public $timestamps = true;

    public function getImageAttribute($val)
    {
        return ($val!=null)? asset('assets/images/category/'.$val):null;
    }

    protected $casts = [
        'features_ids' => 'array',
        'popular_products_ids' => 'array',
    ];
}
