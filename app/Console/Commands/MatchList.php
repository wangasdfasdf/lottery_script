<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MatchList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:list:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private array $urls = [
        [
            'name'  => 'getVtoolsConfigV1',
            'url'   => 'https://webapi.sporttery.cn/gateway/report/getVtoolsConfigV1.qry',
            'param' => [
                'configKey' => 'vtools:config:zc_app_loty_betshu',
            ],
        ],
        [
            'name'  => 'basketballGetMatchCalculatorV1',
            'url'   => 'https://webapi.sporttery.cn/gateway/jc/basketball/getMatchCalculatorV1.qry',
            'param' => [
                'poolCode' => '',
                'channel'  => 'c',
            ],
        ],
        [
            'name'  => 'footballGetMatchCalculatorV1',
            'url'   => 'https://webapi.sporttery.cn/gateway/jc/football/getMatchCalculatorV1.qry',
            'param' => [
                'poolCode' => '',
                'channel'  => 'c',
            ],
        ],
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $version = date('H');

        foreach ($this->urls as $item) {
            $result = Http::get($item['url'], $item['param']);
            Storage::put(sprintf("%s/%s_%s.json", date('Ymd'), $item['name'], $version), $result->body(), 'public');
        }
    }


}
