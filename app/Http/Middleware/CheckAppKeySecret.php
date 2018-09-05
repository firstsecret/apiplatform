<?php

namespace App\Http\Middleware;

use App\Models\AppUser;
use Closure;

class CheckAppKeySecret
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $app_key = urldecode($request->get('app_key'));
        $app_secret = urldecode($request->get('app_secret'));
        // check
//        var_dump($app_key);
//        var_dump($app_secret);
        if (empty($app_key)) {
            return Response()->json(['status_code' => 403, 'msg' => 'appkey必须']);
        } else if (empty($app_secret)) {
            return Response()->json(['status_code' => 403, 'msg' => 'appsecret必须']);
        } else {
            // check app
            $app = AppUser::where([
                ['app_key', '=', $app_key],
                ['app_secret', '=', $app_secret]
            ])->get(['id']);

            if (!$app) return Response()->json(['status_code' => 403, 'msg' => '非法的appkey或appsecret']);
        }

        return $next($request);
    }
}
