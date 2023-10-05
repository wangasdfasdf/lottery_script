<?php

namespace App\Console\Commands;

use App\Enum\OrderType;
use App\Models\MatchResult;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstallMatchResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:result:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '添加比赛结果';
    private string $football_list_url = 'https://webapi.sporttery.cn/gateway/jc/football/getMatchResultV1.qry';
    private string $football_detail_url = 'https://webapi.sporttery.cn/gateway/jc/football/getFixedBonusV1.qry';

    private string $basketball_list_url = 'https://webapi.sporttery.cn/gateway/jc/basketball/getMatchResultV2.qry';
    private string $basketball_detail_url = 'https://webapi.sporttery.cn/gateway/jc/basketball/getFixedBonusV2.qry?';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("install:match:result", ["------------------start----------------------------"]);
        //处理足球
        $matchIds = [];
        $this->getFootMatchIds(1, $matchIds);

        foreach ($matchIds as $matchId) {
            $this->getFootDetail($matchId);
        }

        //处理篮球
        $basketMatchIds = [];
        $this->getBasketMatchIds(1, $basketMatchIds);

        foreach ($basketMatchIds as $basketMatchId) {
            $this->getBasketDetail($basketMatchId);
        }

        Log::info("install:match:result", ["------------------end----------------------------"]);

    }

    /**
     * 获取足球所有有结果的比赛ID
     *
     * @param int $pageNo
     * @param array $arr
     * @created_at 2022/11/20
     */
    public function getFootMatchIds(int $pageNo = 1, array &$arr = [])
    {
        $result = Http::connectTimeout(4)->timeout(3)->get($this->football_list_url, [
            'matchPage'      => 1,
            'matchBeginDate' => now()->subDays(10)->format('Y-m-d'),
            'matchEndDate'   => now()->format('Y-m-d'),
            'leagueId'       => '',
            'pageSize'       => 30,
            'pageNo'         => $pageNo,
            'isFix'          => 0,
            'pcOrWap'        => 1,
        ]);

        $result = json_decode($result->body(), true);

        $arr = \array_merge($arr, array_column($result['value']['matchResult'], 'matchId'));

        foreach ($result['value']['matchResult'] as $item) {

            if ($item['matchResultStatus'] == 2 && $item['sectionsNo999'] == '无效场次') {
                MatchResult::query()->firstOrCreate(
                    [
                        'match_id' => $item['matchId'],
                        'pool_id'  => 0,
                        'type'     => OrderType::FOOTBALL,
                    ], [
                        'code'        => "*",
                        'goal_line'   => "*",
                        'combination' => "*",
                        'pool_status' => "refund",
                    ]
                );
            }

        }


        $pages = $result['value']['pages'];

        $pageNo++;

        if ($pageNo <= $pages) {
            $this->getFootMatchIds($pageNo, $arr);

        }

    }

    /**
     * 获取篮球所有有结果的比赛ID
     *
     * @created_at 2022/11/20
     */
    public function getBasketMatchIds(int $pageNo = 1, array &$arr = [])
    {
        $result = Http::connectTimeout(4)->timeout(3)->get($this->basketball_list_url, [
            'matchPage'      => 1,
            'matchBeginDate' => now()->subDays(2)->format('Y-m-d'),
            'matchEndDate'   => now()->format('Y-m-d'),
            'leagueId'       => '',
            'pageSize'       => 60,
            'pageNo'         => $pageNo,
            'isFix'          => 0,
            'pcOrWap'        => 1,
        ]);

        $result = json_decode($result->body(), true);


        $arr = \array_merge($arr, array_column(array_filter($result['value']['matchResult'], function ($item) {
            return $item['status'] == 2;
        }), 'matchId'));

        $pages = $result['value']['pages'];

        $pageNo++;

        if ($pageNo <= $pages) {
            $this->getBasketMatchIds($pageNo, $arr);

        }
    }

    /**
     * 足球比赛详情
     *
     * @param int $matchId
     * @created_at 2022/11/20
     */
    public function getFootDetail(int $matchId)
    {
        $value = MatchResult::query()->where('match_id', $matchId)->where('type', 'football')->value('id');

        if ($value > 0) {
            return;
        }

        $result = Http::connectTimeout(4)->timeout(3)->get($this->football_detail_url, [
            'clientCode' => '3001',
            'matchId'    => $matchId,
        ]);

        $result = json_decode($result->body(), true);

        $data = Arr::get($result, 'value.matchResultList', []);

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $item) {
            MatchResult::query()->firstOrCreate(
                [
                    'match_id' => $item['matchId'],
                    'pool_id'  => $item['poolId'],
                    'type'     => OrderType::FOOTBALL,
                ], [
                    'code'        => $item['code'],
                    'goal_line'   => $item['goalLine'],
                    'combination' => $item['combination'],
                    'pool_status' => 'payout',
                ]
            );
        }
    }


    /**
     * 篮球比赛详情
     *
     * @param int $matchId
     * @created_at 2022/11/20
     */
    public function getBasketDetail(int $matchId)
    {
        $value = MatchResult::query()->where('match_id', $matchId)->where('type', 'basketball')->value('id');

        if ($value > 0) {
            return;
        }

        $result = Http::connectTimeout(4)->timeout(3)->get($this->basketball_detail_url, [
            'clientCode' => '3001',
            'matchId'    => $matchId,
        ]);

        $result = json_decode($result->body(), true);

        $data = $result['value']['oddsHistory'];

        $matchId = $result['value']['matchInfo']['matchId'];
        foreach ($data as $key => $item) {
            if (in_array($key, ['matchId', 'poolCode', 'singleList'])) {
                continue;
            }
            $code = strtoupper(rtrim($key, 'List'));

            if (in_array($key, ['wnmList', 'mnlList'])) {
                foreach ($item as $item1) {
                    $combination = $item1['combination'];

                    if (empty($item1['combination'])) {
                        return;
                    }

                    $tem = MatchResult::query()->firstOrCreate(
                        [
                            'match_id'  => $matchId,
                            'pool_id'   => 0,
                            'type'      => OrderType::BASKETBALL,
                            'goal_line' => $item1['goalLine'] ?? '',
                            'code'      => $code,
                        ], [
                            'combination' => $item1['combination'],
                        ]
                    );

                    if (empty($tem->combination)) {
                        $tem->combination = $combination;
                        $tem->save();
                    }
                }
            } else {
                foreach ($item as $item1) {
                    foreach ($item1 as $item2) {
                        if (empty($item2['combination'])) {
                            return;
                        }
                        $combination = $item2['combination'];
                        $tem         = MatchResult::query()->firstOrCreate(
                            [
                                'match_id'  => $matchId,
                                'pool_id'   => 0,
                                'type'      => OrderType::BASKETBALL,
                                'goal_line' => $item2['goalLine'],
                                'code'      => $code,
                            ], []
                        );

                        if (empty($tem->combination)) {
                            $tem->combination = $combination;
                            $tem->save();
                        }
                    }
                }
            }
        }
    }
}
