<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;
     protected $fillable=[
        'id',
        'title_ar',
        'title_en',
        'desc_ar',
        'desc_en',
        'image',
        'created_at',
        'updated_at'
    ];

    public $timestamps=true;

    public function getImageAttribute($val)
    {
        return ($val != null) ? asset('assets/images/about/'.$val):null;
    }

     public function scopeSelection($q){
        $local = app()->getLocale();
        return $q->select([
            'id',
            'title_'.$local.' as title',
            'desc_'.$local.' as desc',
            'image',
            'created_at',
            'updated_at',
        ]);
    }
}
