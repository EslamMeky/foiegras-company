<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChekLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       // تحديد اللغة الافتراضية
        $defaultLocale = config('app.locale'); // اللغة الافتراضية من إعدادات التطبيق

        // الحصول على اللغة من الهيدر
        $locale = $request->header('Accept-Language', $defaultLocale);

        // التحقق من أن اللغة المدعومة موجودة
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = $defaultLocale; // إذا لم تكن مدعومة، استخدم اللغة الافتراضية
        }

        // ضبط اللغة في التطبيق
        app()->setLocale($locale);

        return $next($request);
    }
}
