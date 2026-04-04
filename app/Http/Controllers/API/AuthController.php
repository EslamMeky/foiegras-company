<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Mail\OtpMail;
use App\Models\Favourite;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    // public function refresh()
    // {
    //     try {
    //         $token = JWTAuth::refresh(JWTAuth::getToken());
    //         return response()->json([
    //             'token' => $token,
    //             'token_type' => 'bearer',
    //             'expires_in' => config('jwt.ttl') * 60
    //         ]);
    //     } catch (\Exception $ex) {
    //         return $this->ReturnError('401', 'Token cannot be refreshed');
    //     }
    // }

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
    public function myorder()
    {
        try {
           $locale = app()->getLocale();

            // جلب الأوردرات الخاصة باليوزر الحالي فقط
            $orders = Order::where('user_id', auth()->id())
                ->with([
                    'items.product' => function ($query) use ($locale) {
                        // مهم جداً تختار الـ id والـ foreign keys عشان العلاقة متتكسرش
                        $query->select(
                            'id',
                            "name_{$locale} as name",
                            "desc_{$locale} as desc",
                            'image', 'price_discount'
                        );
                    }
                ])
                ->latest() // ترتيب من الأحدث للأقدم
                ->get();

            if ($orders->isEmpty()) {
                return $this->ReturnError(404, __('message.NoOrdersFound'));
            }

            return $this->ReturnData('orders', $orders, '');
        } catch (\Exception $ex) {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function myfavourite()
    {
        try {
           $locale = app()->getLocale();

        // 1. جلب المفضلات الخاصة باليوزر مع بيانات المنتج فوراً
        $favourites = Favourite::where('user_id', auth()->id())
            ->with(['product' => function ($query) use ($locale) {
                $query->select(
                    'id',
                    "name_{$locale} as name",
                    "desc_{$locale} as desc",
                    'image',
                    'price_discount'
                );
            }])
            ->latest()
            ->get();

        // 2. التحقق لو القائمة فاضية (اختياري حسب تصميمك)
        if ($favourites->isEmpty()) {
            return $this->ReturnData('favourites', [], __('message.NoFavouritesYet'));
        }

        return $this->ReturnData('favourites', $favourites, '');

    } catch (\Exception $ex) {
        // نصيحة: $ex->getCode() أحياناً بترجع string أو code مش متوافق مع HTTP
        // يفضل تمرير 500 أو الكود الفعلي بعد التأكد منه
        return $this->ReturnError(500, $ex->getMessage());
    }

    }

    public function sendOtp(Request $request)
    {
         try
        {
            $request->validate(['email' => 'required|email|exists:users,email']);

            $otp = rand(100000, 999999);
            $user = User::where('email', $request->email)->first();

            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // إرسال الإيميل فعلياً
            Mail::to($user->email)->send(new OtpMail($otp));

            // return $this->ReturnSuccessMessage("OTP has been sent to your email.");
            return $this->ReturnSuccess('200',__('message.OTP has been sent to your email.'));
        }
        catch(Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }

    public function verifyOtp(Request $request)
    {
         try
        {
            $request->validate([
                    'email' => 'required|email|exists:users,email',
                    'otp'   => 'required|numeric'
                ]);

                $user = User::where('email', $request->email)
                            ->where('otp_code', $request->otp)
                            ->where('otp_expires_at', '>', Carbon::now())
                            ->first();

                if (!$user) {
                    // return $this->ReturnError(400, "Invalid or expired OTP.");
                    return $this->ReturnError(400, __('message.Invalid or expired OTP.'));
                }

                return $this->ReturnSuccess('200',__('message.OTP is valid.'));
                // return $this->ReturnSuccessMessage("");
        }
        catch(Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }


        public function resetPassword2(Request $request)
    {
        try
        {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|numeric',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp_code', $request->otp)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            // return $this->ReturnError(400, "Action failed. Invalid OTP or session expired.");
            return $this->ReturnError(400, __('message.Action failed. Invalid OTP or session expired.'));
        }

        // تحديث الباسورد وتصفير حقول الـ OTP للأمان
        $user->update([
            'password' => Hash::make($request->new_password),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return $this->ReturnSuccess('200',__('message.Password updated successfully.'));
        // return $this->ReturnSuccessMessage("Password updated successfully.");
        }
        catch(Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }


    public function deleteAccount(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->returnValidationError(422, $validator);
            }


            $user = auth()->user();


            if ($user->email !== $request->email) {
                return $this->ReturnError(403, __('message.Unauthorized: You can only delete your own account.'));
            }


            if (!Hash::check($request->password, $user->password)) {
                return $this->ReturnError(401, __('message.The password provided is incorrect.'));
            }

            $user->delete();
            auth()->logout();

            return $this->ReturnSuccess(200, __('message.Your account has been permanently deleted.'));

        } catch (\Exception $ex) {
            return $this->ReturnError($ex->getCode(), $ex->getMessage());
        }
    }




}
