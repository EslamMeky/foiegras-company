<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use GeneralTrait;
    public function register(Request $request)
    {
        try
        {
        //     $otp = rand(100000, 999999);
        //     $otpExpiry = now()->addMinutes(5);

         $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
                'phone' => 'nullable|string|max:20',
                'gender' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $pathFile="";
            if ($request->hasFile('image')){
                 $pathFile=uploadImage('user',$request->image);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => "Client",
                'gender' => $request->gender,
                'address' => $request->address,
                'image' => $pathFile,
                'password' => bcrypt($request->password),
            ]);
            // Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
            //     $message->to($user->email)->subject('Verify Your Email');
            // });
            return $this->ReturnSuccess(201,('Regester Successfully'));
        }

        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) ) {
            return $this->ReturnError('401',__('message.invalidCerdentails'));
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user'=>$user,
        ]);
    }

    public function logout()
    {
        $auth=auth();
        if(!$auth)
        {
            return $this->ReturnError('404','invaild token');
        }
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

     public function resetPassword(Request $request)
    {
        try {
            // التحقق من صحة البيانات المدخلة
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|exists:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::where('email', $request->email)->first();

            // التحقق من صلاحية الـ OTP
            if ($user->email != $request->email) {
                return $this->ReturnError('404','Not Found Email');
            }

            // تحديث كلمة المرور
                $user->update([
                    'password' => bcrypt($request->password),

                ]);

            return $this->ReturnSuccess(200, ('Reset Password Successfully'));
        } catch (\Exception $ex) {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        try
        {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string|max:20',
                'gender' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $user=auth()->user();
            if (!$user) {
               return $this->ReturnError('404',__('message.NotFoundUser'));
            }
            if ($request->hasFile('image')){
                $photoPath = parse_url($user->image, PHP_URL_PATH);
                $photoPath = ltrim($photoPath, '/');
                $oldImagePath = public_path($photoPath);

                if ($user->image && file_exists($oldImagePath))
                {

                    unlink($oldImagePath);
                }
                $pathFile= uploadImage('user',$request->image);
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role' => "Client",
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'image' => $pathFile,
                ]);
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => "Client",
                'gender' => $request->gender,
                'address' => $request->address,
            ]);


            return $this->ReturnSuccess(200,__('message.update'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(), $ex->getMessage());

        }
    }

    public function singleUser()
    {
        try {
            $locale = app()->getLocale();

            // جلب المستخدم المصادق عليه
          $user = User::with([
                'favourites',
                'orders',
                'orders.items',
                'orders.items.product' => function ($query) use ($locale) {
                    $query->select(
                        'id',
                        'name_'.$locale.' as name',
                        'desc_'.$locale.' as desc',
                        'image',
                        'price_discount',
                        'weight',
                        'note',
                    );
                }
            ])->find(auth()->id());

            // التحقق من وجود المستخدم
            if (!$user) {
                return $this->ReturnError(404, __('message.NotFoundUser'));
            }

            return $this->ReturnData('user', $user, '');
        } catch (\Exception $ex) {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }


}
