<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    use GeneralTrait;
     public function save(Request $request)
    {
        try
        {
            $rules = [
                'name' => 'required',
                'message' => 'required',
                'subject' => 'required',
                'email' => 'required|email',

            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
            {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            ContactUs::create([
                'name'=>$request->name,
                'message'=>$request->message,
                'email'=>$request->email,
                'subject'=>$request->subject,
            ]);
            return $this->ReturnSuccess(200,__('message.saved'));
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());

        }
    }

}
