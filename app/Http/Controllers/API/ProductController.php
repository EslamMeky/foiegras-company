<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use GeneralTrait;

    public function singleProductWithRelated($name_product)
    {
        try {
            $locale = app()->getLocale();

            // جلب المنتج الرئيسي
            $product = Product::with([
                'category'=> function ($query) use ($locale) {
                    $query->select([
                        'id',
                        'title_'.$locale.' as title',
                        'desc_'.$locale.' as desc',
                        'image'

                    ]);
                },
                'reviews'])
                ->withExists([
                    'favourites as is_favourite' => function ($q) {
                        $q->where('user_id', auth('api')->id() ?? 0);
                    }
                ])
                ->selection()
                // ->where('name_'. $locale.' as name', $name_product)->first();
                ->where('id', $name_product)->first();

            // إذا لم يتم العثور على المنتج
            if (!$product) {
                return $this->ReturnError(404, __('message.NotFoundProduct'));
            }

            // جلب المنتجات ذات الصلة
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id) // استثناء المنتج الأساسي
                ->selection()
                ->latest()
                ->take(5)
                ->get();
            $reviewCount = $product->reviews()->count();
            $averageRating = $product->reviews->isNotEmpty()
                ? round($product->reviews()->avg('rating'), 2)
                : 0;

            // إعداد البيانات للإرجاع
            $data = [
                'average_rating' => $averageRating,
                'review_count' => $reviewCount,
                'product' => $product,
                'related_products' => $relatedProducts
            ];

            return $this->ReturnData('data', $data, '');
        } catch (\Exception $ex) {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function allProducts(Request $request)
    {
        try {
            $locale = app()->getLocale();

            // الحصول على المدخلات من الـ Request
            $searchTerm = $request->input('search'); // اسم المنتج أو الفئة
            $category = $request->input('category'); // الفئة
            $priceOrder = $request->input('price_order');


            // بناء الاستعلام
            $query = Product::with([
                'category' => function ($query) use ($locale) {
                $query->select([
                        'id',
                        'title_'.$locale.' as title',
                        'desc_'.$locale.' as desc',
                        'image'

                    ]); // إرجاع اسم الفئة بناءً على اللغة
            }])
            ->withExists([
                'favourites as is_favourite' => function ($q) {
                    $q->where('user_id', auth('api')->id() ?? 0);
                }
            ])
            ->Selection();

            // إضافة شرط البحث في اسم المنتج
            if ($searchTerm) {
                $query->where('name_' . $locale, 'like', '%' . $searchTerm . '%');
            }

            // إضافة شرط البحث في الفئة
            if ($category) {
                $query->whereHas('category', function ($query) use ($category, $locale) {
                    $query->where('title_' . $locale, 'like', '%' . $category . '%');
                });
            }

            // إضافة ترتيب السعر إذا تم تحديده
            if ($priceOrder) {
                if ($priceOrder == 'asc') {
                    $query->orderBy('price_discount', 'asc');
                } elseif ($priceOrder == 'desc') {
                    $query->orderBy('price_discount', 'desc');
                }
            }

            // جلب المنتجات بناءً على الاستعلام
            $products = $query->latest()->paginate(10);

            foreach ($products as $product) {
                // حساب التقييمات
                $reviewCount = $product->reviews()->count();
                $product->average_rating = $reviewCount > 0 ? round($product->reviews()->avg('rating'), 2) : 0;
                $product->review_count = $reviewCount;

                // إضافة المنتجات ذات الصلة
                $relatedProducts = Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id) // استثناء المنتج نفسه
                    ->Selection()
                    ->latest()
                    ->take(5)
                    ->get();

                // إضافة المنتجات ذات الصلة لكل منتج
                $product->related_products = $relatedProducts;
            }

            return $this->ReturnData('products', $products, '');

        } catch (\Exception $ex) {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }


    public function TopSeller()
    {
         try
        {
        $local = app()->getLocale();

            $products = Product::withSum('orderItems', 'quantity')
            ->withExists([
                'favourites as is_favourite' => function ($q) {
                    $q->where('user_id', auth('api')->id() ?? 0);
                }
            ])
            ->addSelect([
                'id',
                'category_id',
                'name_'.$local. ' as name',
                'desc_'.$local. ' as desc',
                'main_price',
                'price_discount',
                'weight',
                'note',
                'stock',
                'outOfStock',
                'barcode',
                'image',
                // 'otherImage',
                'created_at',
                'updated_at'

                ])
            ->orderByDesc('order_items_sum_quantity')
            ->take(5)
            ->get();

            return $this->ReturnData('products', $products, '');

        }
        catch(Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }



    }
    public function OffersProduct()
    {
         try
        {
        $offers = Product::topOffers();
        return $this->ReturnData('offers', $offers, '');
            // return $this->ReturnData('products', $products, '');

        }
        catch(Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }


}
