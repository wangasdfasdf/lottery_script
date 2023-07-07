<?php

namespace App\Services;

use App\Enum\Status;
use App\Models\Admin;
use App\Models\AdminUser;
use App\Traits\Token;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Wang9707\MakeTable\Exceptions\TipsException;
use Wang9707\MakeTable\Services\Service;


class AdminUserAuthService extends Service
{
    use Token;

    /**
     * 登录
     *
     * @param string $phone
     * @param string $password
     * @return Model|AdminUser|Builder|null
     * @throws TipsException
     */
    public function login(string $LoginName, string $password): Model|AdminUser|Builder|null
    {
        /**
         * @var AdminUser $admin
         */
        $admin = AdminUser::query()->where('login_name', $LoginName)->first();

        if (empty($admin)) {
            $this->throw('账号错误');
        }

        if (!Hash::check($password, $admin->password)) {
            $this->throw('密码错误');
        }


        $admin->token = $this->getToken($admin);
        $admin->save();

        return $admin;
    }


}
