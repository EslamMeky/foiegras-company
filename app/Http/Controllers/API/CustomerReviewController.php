<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\CustomerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerReviewController extends Controller
{
    use GeneralTrait;

     public function store(Request $request)
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

            $review = CustomerReview::create([
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


    public function show()
    {
        try
        {
            // $user=auth()->user()->id();
            // جلب التقييمات مع المستخدمين
            $reviews = CustomerReview::with(['users'])
                ->paginate(10);

            return $this->ReturnData('reviews', $reviews, '');
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }


}
