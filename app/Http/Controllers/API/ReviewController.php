<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    use GeneralTrait;

     public function store(Request $request, $productId)
    {
        try
        {
             $rules = [
                'rating' => 'required',
                'review' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $product = Product::findOrFail($productId);
            if(!$product){
                return $this->ReturnError('404',__('message.NotFoundProduct'));
            }
            $review = Review::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'rating' =>$request->rating,
                'review' => $request->review,
            ]);

           return $this->ReturnSuccess(201,__('message.saved'));
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }


    public function show($productId)
    {
        try
        {
            $product = Product::find($productId);
            if(!$product){
                return $this->ReturnError('404',__('message.NotFoundProduct'));
            }
            // جلب التقييمات مع المستخدمين
            $reviews = Review::where('product_id', $product->id)
                ->with(['users'])
                ->get();

            // حساب عدد المستخدمين الذين قاموا بالتقييم
            $uniqueUsersCount = Review::where('product_id', $product->id)->distinct('user_id')->count('user_id');

            // حساب المتوسط للتقييمات
            $averageRating = round(Review::where('product_id', $product->id)->avg('rating') ?? 0);


            $data=[
             'users_counts'=>$uniqueUsersCount,
            'averageRating'=>$averageRating,
             'review'=>$reviews,
           ];
            return $this->ReturnData('data', $data, '');
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }

     public function showAll()
    {
        try
        {
            $locale = app()->getLocale();
            $reviews = Review::with([
                'users',
                'products' => function ($query) use ($locale) {
                $query->select([
                    'id',
                    'category_id',
                    'name_'.$locale.' as name',
                    'desc_'.$locale.' as desc'
                 ]);
                },
                'products.category' => function ($query) use ($locale) {
                    $query->select([
                        'id',
                        'title_'.$locale.' as title'
                    ]);
                }
            ])
                ->latest()
                ->paginate(10);
            $userCount = Review::distinct('user_id')->whereNotNull('user_id')->count('user_id');

            // حساب المتوسط
            $averageRating = round(Review::avg('rating') ?? 0);

            $data=[
                'users_counts'=>$userCount,
                'averageRating'=>$averageRating,
                'review'=>$reviews,

            ];
            return $this->ReturnData('data', $data, '');
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());

        }

    }


}
