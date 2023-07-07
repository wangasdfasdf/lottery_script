<?php

namespace App\Http\Middleware;

use App\Enum\Status;
use App\Models\Shop;
use App\Services\AdminUserAuthService;
use Closure;
use Illuminate\Http\Request;
use Wang9707\MakeTable\Response\Response;

class CheckShop
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
        $token     = $request->header('token');
        $machineId = $request->header('machineId');

        if (empty($machineId)) {
            return Response::error('请传递设备ID', [], 401);
        }

        $result = AdminUserAuthService::instance()->checkToken($token);


        $shopId = $result['model_id'] ?? 0;

        if (empty($shopId)) {
            return Response::response(401, '请重新登录', [], 401);
        }

        $shop = Shop::query()->find($shopId);

        if (Status::OFF->value == $shop?->status) {
            return Response::error('账号已被禁用,请联系管理员');
        }

        if (now() > $shop->expiry_time) {
            return Response::error('账号已过期,请联系管理员');
        }


        $request->offsetSet('shop_id', $shopId);

        /**
         * @var Shop $shop
         */
        $shop = Shop::query()->find($shopId);
        if ($shop->machine_id != $machineId) {
            return Response::error('请原设备登录', [], 401);
        }

        return $next($request);
    }
}
