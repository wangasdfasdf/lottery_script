<?php

namespace App\Services;

use App\Enum\Status;
use App\Models\Admin;
use App\Models\AdminUser;
use App\Models\Shop;
use App\Traits\Token;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Wang9707\MakeTable\Exceptions\TipsException;
use Wang9707\MakeTable\Response\Response;
use Wang9707\MakeTable\Services\Service;


class ShopAuthService extends Service
{
    use Token;

    /**
     * 登录
     *
     * @param string $LoginName
     * @param string $password
     * @param string $machineId
     * @return Model|Shop|Builder|null
     * @throws TipsException
     */
    public function login(string $LoginName, string $password, string $machineId): Model|Shop|Builder|null
    {
        /**
         * @var Shop $shop
         */
        $shop = Shop::query()->where('login_name', $LoginName)->first();

        if (empty($shop)) {
            $this->throw('账号错误');
        }

        if (!Hash::check($password, $shop->password)) {
            $this->throw('密码错误');
        }

        if (Status::OFF->value == $shop->status) {
            $this->throw('账号已被禁用,请联系管理员');
        }

        if (now() > $shop->expiry_time) {
            $this->throw('账号已过期,请联系管理员', 3000);
        }

        if (empty($shop->machine_id)) {
            $shop->machine_id = $machineId;
            $shop->save();
        }

        if ($shop->machine_id != $machineId) {
            $this->throw('请原设备登录');
        }


        $shop->token = $this->getToken($shop);
        $shop->save();

        return $shop;
    }


}
