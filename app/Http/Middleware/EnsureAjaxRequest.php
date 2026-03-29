<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAjaxRequest
{
    /**
     * منع الدخول المباشر للمسارات المخصصة للـ AJAX فقط.
     * أي طلب مش جاي من JavaScript (مش XMLHttpRequest) هيتصد.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->ajax()) {
            abort(403, 'هذا المسار مخصص للطلبات الداخلية فقط.');
        }

        return $next($request);
    }
}
