<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertising extends Model
{
    use HasFactory;

     protected $fillable=[
        'id',
        'title_ar',
        'title_en',
        'desc_ar',
        'desc_en',
        'btn_ar',
        'btn_en',
        'link',
        'status',
        'image',
        'created_at',
        'updated_at'
    ];

    public $timestamps=true;

    public function getImageAttribute($val)
    {
        return ($val != null) ? asset('assets/images/advertising/'.$val):null;
    }

     public function scopeSelection($q){
        $local = app()->getLocale();
        return $q->select([
        'id',
        'title_'.$local. ' as title',
        'desc_'.$local. ' as desc',
        'btn_'.$local. ' as btn',
        'link',
        'status',
        'image',
        'created_at',
        'updated_at'
        ]);
    }
}
