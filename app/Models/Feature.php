<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable=[
        'id',
        'title_ar',
        'title_en',
        'created_at',
        'updated_at'
    ];
     public $timestamps = true;

     public function scopeSelection($q){
        $local = app()->getLocale();
        return $q->select([
            'id',
            'title_'.$local.' as title',
            'created_at',
            'updated_at',
        ]);
    }
}
