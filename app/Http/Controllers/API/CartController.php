<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Cart;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use GeneralTrait;

    // public function addToCart(Request $request, $productId)
    // {
    //      try
    //     {
    //         $user = $request->user();
    //     $product = Product::findOrFail($productId);

    //     // التأكد من الكمية
    //     if ($request->quantity > $product->stock) {
    //         return $this->ReturnError(400, __('message.outofstock'));
    //     }

    //     // تحقق إذا المنتج موجود بالفعل في الكارت
    //     $cart = Cart::where('user_id', $user->id)
    //         ->where('product_id', $productId)
    //         ->first();

    //     if ($cart) {
    //         // تحديث الكمية
    //         $cart->quantity += $request->quantity;
    //     } else {
    //         // إنشاء سجل جديد في الكارت
    //         $cart = new Cart([
    //             'user_id' => $user->id,
    //             'product_id' => $product->id,
    //             'quantity' => $request->quantity,
    //         ]);
    //     }

    //     // تحديث السعر والـ subtotal
    //     $cart->price = $product->price_discount;
    //     $cart->subtotal = $cart->quantity * $cart->price;
    //     $cart->save();

    //     // جلب بيانات الكارت مع المنتج حسب اللغة
    //     $locale = app()->getLocale();
    //     $cartItems = Cart::with(['product' => function($query) use ($locale) {
    //         $query->select(
    //             'id',
    //             'name_' . $locale . ' as name',
    //             'desc_' . $locale . ' as desc',
    //             'image',
    //             'price_discount',
    //             'stock'
    //         );
    //     }])
    //     ->where('user_id', $user->id)
    //     ->get();
    //         return $this->ReturnData('cartItem',$cart,'Product added to cart');
    //     }
    //     catch(Exception $ex)
    //     {
    //         return $this->ReturnError($ex->getCode(),$ex->getMessage());
    //     }

    // }


    public function addToCart(Request $request, $productId)
{
    try {

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'weight'   => 'required|string'
        ]);

        $user = $request->user();
        $product = Product::findOrFail($productId);

        // الحصول على بيانات الوزن من JSON
        $weights = collect($product->weight);
        $weightData = $weights->firstWhere('weight', $request->weight);

        if (!$weightData) {
            return $this->ReturnError(400, 'Invalid weight');
        }

        $price = $weightData['price'];

        // البحث عن نفس المنتج بنفس الوزن في الكارت
        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('weight', $request->weight)
            ->first();

        // تحديد الكمية الجديدة
        $newQuantity = $cart
            ? $cart->quantity + $request->quantity
            : $request->quantity;

        // التحقق من المخزون
        if ($newQuantity > $product->stock) {
            return $this->ReturnError(400, __('message.outofstock'));
        }

        if ($cart) {

            // تحديث الكمية
            $cart->quantity = $newQuantity;

        } else {

            // إنشاء سجل جديد في الكارت
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->product_id = $product->id;
            $cart->quantity = $request->quantity;
            $cart->weight = $request->weight;
        }

        // تحديث السعر والحساب
        $cart->price = $price;
        $cart->subtotal = $cart->quantity * $price;

        $cart->save();

        return $this->ReturnData('cartItem', $cart, 'Product added to cart');

    } catch (\Exception $ex) {

        return $this->ReturnError($ex->getCode(), $ex->getMessage());
    }
}


    public function cartItems(Request $request)
    {
        $user = $request->user();
        $local = app()->getLocale();
        $cartItems = Cart::with([
            'product'=> function($query) use ($local) {
                    $query->select(
                        'id',
                        'name_' . $local . ' as name',
                        'desc_' . $local . ' as desc',
                        'image',
                        'weight',
                        'price_discount',
                        'stock'
                    );
                }
            ])
        ->where('user_id', $user->id)->get();

        return $this->ReturnData('cartItem',$cartItems,'Product in Cart for User');


    }

    // public function viewCart()
    // {
    //     try
    //     {
    //         $local = app()->getLocale(); // الحصول على اللغة الحالية

    //         $cartItems = Cart::with([
    //             'product' => function($query) use ($local) {
    //                 $query->select(
    //                     'id',
    //                     'name_' . $local . ' as name',
    //                     'desc_' . $local . ' as desc',
    //                     'image',
    //                     'weight',
    //                     'price_discount',
    //                     'stock',
    //                 );
    //             }
    //         ])->where('user_id', auth()->id())
    //             ->latest()
    //             ->get();


    //         $total = $cartItems->sum(function ($item) {
    //             return $item->product->price_discount * $item->quantity;
    //         });

    //         $data=[
    //             'cartItems'=>$cartItems,
    //             'total'=>$total
    //         ];
    //         return $this->ReturnData('data',$data,'');
    //     }
    //     catch (\Exception $ex)
    //     {
    //         return $this->ReturnError($ex->getCode(),$ex->getMessage());
    //     }

    // }

    public function viewCart()
{
    try {

        $local = app()->getLocale();

        $cartItems = Cart::with([
            'product' => function($query) use ($local) {
                $query->select(
                    'id',
                    'name_' . $local . ' as name',
                    'desc_' . $local . ' as desc',
                    'image',
                    'weight',
                    'stock'
                );
            }
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

        // ✅ الحساب الصحيح
        $total = $cartItems->sum('subtotal');

        $data = [
            'cartItems' => $cartItems,
            'total' => $total
        ];

        return $this->ReturnData('data', $data, '');

    } catch (\Exception $ex) {
        return $this->ReturnError($ex->getCode(), $ex->getMessage());
    }
}

   public function updateQuantity(Request $request)
    {
    try {
        // التحقق من صحة البيانات
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'weight'     => 'required|string',
        ]);

        $user = $request->user();

        // البحث عن المنتج في الكارت
        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('weight', $request->weight) // ✅ حسب الوزن
            ->first();

        if (!$cart) {
            return $this->ReturnError(404, __('message.NotFoundProduct'));
        }

        $product = Product::findOrFail($request->product_id);

        // التحقق من الكمية بالنسبة للمخزون
        if ($request->quantity > $product->stock) {
            return $this->ReturnError(400, __('message.outofstock'));
        }

        // تحديث الكمية والسعر والـ subtotal
        $cart->quantity = $request->quantity;
        $cart->price = $product->price_discount;
        $cart->subtotal = $cart->quantity * $cart->price;
        $cart->save();

        // جلب بيانات الكارت بعد التحديث مع المنتج حسب اللغة
        $locale = app()->getLocale();
        $cartItem = Cart::with(['product' => function($query) use ($locale) {
            $query->select(
                'id',
                'name_' . $locale . ' as name',
                'desc_' . $locale . ' as desc',
                'image',
                'weight',
                'price_discount',
                'stock'
            );
        }])
        ->where('id', $cart->id)
        ->first();

        return $this->ReturnData('cartItem', $cartItem, __('message.update'));

    } catch (\Exception $ex) {
        return $this->ReturnError($ex->getCode(), $ex->getMessage());
    }
    }

    public function removeFromCart(Request $request, $productId)
{
    try {

        $request->validate([
            'weight' => 'required|string'
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->where('weight', $request->weight)
            ->first();

        if (!$cart) {
            return $this->ReturnError(404, __('message.NotFoundProduct'));
        }

        $cart->delete();

        return $this->ReturnSuccess(200, __('message.deleted'));

    } catch (\Exception $ex) {
        return $this->ReturnError($ex->getCode(), $ex->getMessage());
    }
}


}
