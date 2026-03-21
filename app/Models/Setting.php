<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'logo',
        'slug_ar',
        'slug_en',
        'desc_ar',
        'desc_en',
        'face',
        'insta',
        'tiktok',
        'whats',
        'location_ar',
        'location_en',
        'phone',
        'email',
        'created_at',
        'updated_at'
    ];


    public function scopeSelection($q)
    {
        $local = app()->getLocale();
        return $q->addSelect([
        'id',
        'logo',
        'slug_'.$local. ' as slug',
        'desc_'.$local. ' as desc',
        'location_'.$local. ' as location',
        'face',
        'insta',
        'whats',
        'tiktok',
        'phone',
        'email',
        'created_at',
        'updated_at'

        ]);
    }
    public $timestamps = true;

    public function getLogoAttribute($val)
    {
        return ($val != null) ? asset('assets/images/setting/'.$val):null;
    }
}


