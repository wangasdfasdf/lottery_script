<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait Token
{
    /**
     * 获取token
     *
     * @param Model $model
     * @return string
     */
    public function getToken(Model $model): string
    {
        $token = md5($model->id . time() . mt_rand(0, 99999)) . $model->getTable();

        $this->setCache($token, $model);

        return $token;
    }

    public function setCache($token, Model $model)
    {
        $aa = Cache::put($token, [
            'token'       => $token,
            'model_id'    => $model->id,
            'create_time' => time(),
            'table'       => $model->getTable(),
        ], 86400);

    }

    /**
     * 检查token
     * @param string $token
     * @return mixed
     */
    public function checkToken(string $token): mixed
    {
        return Cache::get($token);
    }
}
