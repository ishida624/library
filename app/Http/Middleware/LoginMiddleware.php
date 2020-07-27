<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Http\Request;

class LoginMiddleware
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
        #check token
        $token = $request->header('userToken');
        $data = User::where('remember_token', $token)->first();
        $Lv = $data->Lv;
        $request->merge(['UserData' => $data]);
        // dd($Lv);
        // dd($data->remember_token);
        // dd($tokenTime, date('Y-m-d H:i:s'));
        // foreach ($data as  $value) {
        if (isset($data->remember_token)) {
            $tokenTime = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($data->updated_at)));
            if ($tokenTime < date('Y-m-d H:i:s')) {
                return response()->json(['message' => 'Unauthorized', 'reason' => 'token out time'], 401);
            } else {
                return $next($request);
            }
        } else {
            // return response('token false', 403);
            return response()->json(['message' => 'Unauthorized', 'reason' => 'token false'], 401);
        }
        // }
    }
}
