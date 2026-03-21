<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'email',
        'subject',
        'message',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}
