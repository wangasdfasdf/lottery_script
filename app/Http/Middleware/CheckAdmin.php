<?php

namespace App\Http\Middleware;

use App\Services\AdminUserAuthService;
use Closure;
use Illuminate\Http\Request;
use Wang9707\MakeTable\Response\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request                                                                          $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('token');

        $result = AdminUserAuthService::instance()->checkToken($token);

        $adminId = $result['model_id']??0;

        if (empty($adminId)) {
            return Response::response(401, '请重新登录', [], 401);
        }

        $request->offsetSet('admin_id', $adminId);
        return $next($request);
    }
}
