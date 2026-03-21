<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory;
    use Notifiable;
    protected $fillable=[
        'id',
        'category_id',
        'name_ar',
        'name_en',
        'desc_ar',
        'desc_en',
        'main_price',
        'price_discount',
        'weight',
        'note',
        'stock',
        // 'outOfStock',
        'barcode',
        'image',
        // 'otherImage',
        'created_at',
        'updated_at'
    ];

    public $timestamps= true;

    public function getImageAttribute($val)
    {
        return ($val != null) ? asset('assets/images/product/'.$val):null;
    }

    public function getOtherImageAttribute($val)
    {
        if ($val != null) {
            $images = json_decode($val, true); // فك تشفير JSON إلى Array
            if (is_array($images)) {
                return array_map(function ($image) {
                    return asset('assets/images/product/' . $image); // تعديل المسار لإضافة المجلد الناقص
                }, $images);
            }
        }
        return [];
    }


    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class,'product_id');
    }

    protected $casts = [
    'otherImage' => 'array',
     'weight' => 'array',
    ];

    public function getWeightAttribute($val)
    {
        return $val ? json_decode($val, true) : [];
    }

    public function setWeightAttribute($val)
    {
        $this->attributes['weight'] = $val ? json_encode($val) : null;
    }

    public function scopeSelection($q)
    {
        $local = app()->getLocale();
        return $q->addSelect([
        'id',
        'category_id',
        'name_'.$local. ' as name',
        'desc_'.$local. ' as desc',
        'main_price',
        'price_discount',
        'weight',
        'note',
        'stock',
        // 'outOfStock',
        'barcode',
        'image',
        // 'otherImage',
        'created_at',
        'updated_at'

        ]);
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

    public function getDiscountPercentageAttribute()
    {
        if ($this->main_price && $this->price_discount) {
            return round((($this->main_price - $this->price_discount) / $this->main_price) * 100, 2);
        }
        return 0;
    }

    public function scopeTopOffers($query)
    {
            $local = app()->getLocale();

            return $query->whereNotNull('main_price')
                        ->whereNotNull('price_discount')
                        ->select([
                            'id',
                            'category_id',
                            'name_'.$local.' as name',
                            'desc_'.$local.' as desc',
                            'main_price',
                            'price_discount',
                            'weight',
                            'note',
                            'stock',
                            // 'outOfStock',
                            'barcode',
                            'image',
                            'created_at',
                            'updated_at',
                        ])
                        ->get()
                        ->map(function($product) {
                            $product->discount_percentage = round(
                                (($product->main_price - $product->price_discount) / $product->main_price) * 100,
                                2
                            );
                            return $product;
                        });
    }

    public function favourites()
    {
    return $this->hasMany(Favourite::class,'product_id');
    }
}
