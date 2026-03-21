<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
     protected $fillable = [
        'id',
        'user_id',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'payment_method',
        'payment_status',
        'transaction_id',
        'order_status',
        'subtotal',
        'shipping_fee',
        'total',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class,'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
