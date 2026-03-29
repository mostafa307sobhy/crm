<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // لو مش مسجل دخول، أو مسجل بس مش أدمن -> اطرده فوراً (403)
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'عفواً، هذه الصلاحية مخصصة للإدارة فقط.');
        }

        // لو أدمن -> خليه يكمل شغله عادي
        return $next($request);
    }
}