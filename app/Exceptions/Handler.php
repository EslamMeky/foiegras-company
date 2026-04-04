<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
   public function register(): void
   {
    // استخدم Throwable بدل Exception
        $this->renderable(function (\Throwable $e, $request) {

            if ($request->is('api/*')) {

                // 1. لو التوكن منتهي
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    return response()->json(['status' => false, 'message' => 'Token has expired'], 401);
                }

                // 2. لو التوكن غير صالح
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                    return response()->json(['status' => false, 'message' => 'Token is invalid'], 401);
                }

                // 3. دي الأهم: الـ Middleware غالباً بيرمي دي لما التوكن يبوظ أو يختفي
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {

                    // بنختبر الرسالة الداخلية عشان نعرف السبب الحقيقي
                    if ($e->getPrevious() instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                        return response()->json(['status' => false, 'message' => 'Token has expired'], 401);
                    } else if ($e->getPrevious() instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                        return response()->json(['status' => false, 'message' => 'Token is invalid'], 401);
                    }

                    return response()->json(['status' => false, 'message' => 'Unauthorized: Token not provided or invalid'], 401);
                }
            }
        });
    }
}
