<?php

namespace App\Services;

use App\Models\Config;
use Wang9707\MakeTable\Services\Service;


class ConfigService extends Service
{
    public $model = '\App\Models\Config';

    /**
     * 获取配置
     *
     * @param string $filed
     * @return object|null
     */
    public function getField(string $filed): object|null
    {
        return Config::query()->where('key', $filed)->first();
    }
}
