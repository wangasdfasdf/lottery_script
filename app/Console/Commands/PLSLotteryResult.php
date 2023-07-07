<?php

namespace App\Console\Commands;

use App\Models\PlsResult;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PLSLotteryResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pls:lottery:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected string $url = "https://webapi.sporttery.cn/gateway/lottery/getHistoryPageListV1.qry";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = Http::get($this->url, [
            'gameNo'     => 35,
            'provinceId' => 0,
            'pageSize'   => 30,
            'isVerify'   => 1,
            'pageNo'     => 1,
        ]);

        $data = \json_decode($result->body(), true);

        if ($result->status() != 200 || !Arr::get($data, 'success')) {
            return;
        }

        $list = Arr::get($data, 'value.list');

        foreach ($list as $item) {

            $prizeLevelList = \array_column($item['prizeLevelList'], 'stakeAmount', 'prizeLevel');

            PlsResult::query()->firstOrCreate([
                'drawn_um' => $item['lotteryDrawNum'],
            ], [
                'draw_result'    => $item['lotteryDrawResult'],
                'stake_amount_1' => \str_replace(',', '', $prizeLevelList['直选']),
                'stake_amount_2' => \str_replace(',', '', $prizeLevelList['组选3']),
                'stake_amount_3' => \str_replace(',', '', $prizeLevelList['组选6']),
                'result'         => $item,
            ]);
        }
    }
}
