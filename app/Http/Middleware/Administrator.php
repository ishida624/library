<?php

namespace App\Http\Middleware;

use Closure;

class Administrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $UserData = $request->input('UserData');
        $Lv = $UserData->Lv;
        if ($Lv === 3) {
            return $next($request);
        } else {
            return response()->json(['message' => 'Forbidden', 'reason' => 'Permission denied'], 403);
        }
    }
}
