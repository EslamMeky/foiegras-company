<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    use GeneralTrait;

         public function checkout(Request $request)
    {
        try {

            $user = $request->user();

            $cartItems = Cart::where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return $this->ReturnError(400, 'Cart is Empty');
            }

            DB::beginTransaction();

            $subtotal = $cartItems->sum('subtotal');
            $shipping = $request->shipping_fee ?? 0;

            /*
            إنشاء الطلب
            */

            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'total' => $subtotal + $shipping,
                'notes' => $request->notes ?? null
            ]);

            /*
            إنشاء order_items
            */
            foreach ($cartItems as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'weight' => $item->weight, // ✅ مهم جدا
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal
                ]);

                if ($request->payment_method == "cod") {

                    $product = Product::find($item->product_id);

                    if (!$product || $product->stock < $item->quantity) {

                        DB::rollBack();

                        return $this->ReturnError(400, 'Product out of stock');
                    }

                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }
            /*
            مسح الكارت
            */

            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            /*
            الرد حسب طريقة الدفع
            */

            if ($request->payment_method == "cod") {

                return $this->ReturnData(
                    'order',
                    $order,
                    'Order Created Successfully - Cash On Delivery'
                );
            }

            if ($request->payment_method == "paymob") {

                return $this->ReturnData(
                    'order',
                    $order,
                    'Order Created - Redirect To Paymob'
                );
            }

        } catch (\Exception $ex) {

            DB::rollBack();

            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }

}
