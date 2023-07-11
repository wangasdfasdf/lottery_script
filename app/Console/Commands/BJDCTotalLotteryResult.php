<?php

namespace App\Console\Commands;

use App\Models\BjdcResult;
use http\Env\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BJDCTotalLotteryResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bjdc:total:lottery:result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '北京单场';

    protected string $gameinfo_his = "https://www.bjlot.com/ssm/@type/html/gameinfo_his.txt";
    protected string $parlayGetGame = "https://www.bjlot.com/data/@typeParlayGetGame_@draw.xml";

    protected array $types = ['200', '250', '230', '240', '210', '270'];
    //历史开奖url
    protected string $historyUrl = "https://www.bjlot.com/data/@type/draw/@year/@awardPeriod.js";
    protected string $historyYearUrl = "https://www.bjlot.com/data/@type/control/drawyearlist.js";
    protected string $historyMonthUrl = "https://www.bjlot.com/data/@type/control/@year.js";
    protected string $historyLastAwardPeriodUrl = "https://www.bjlot.com/data/@type/control/drawnolist_@year@month.js";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //历史奖期
        $this->getHistory();

        //当前奖期
        $this->dcParlayGetGame();
    }

    public function dcParlayGetGame()
    {
        foreach ($this->types as $type) {
            $draw = $this->gameinfo_his($type);

            $this->parlayGetGame($draw, $type);
        }
    }

    //获取历史开奖记录
    public function getHistory()
    {
        $cacheKey = "bjdc:url:cache:@url";
        foreach ($this->types as $type) {

            $year        = $this->historyYear($type);
            $month       = $this->historyMonth($year, $type);
            $awardPeriod = $this->historyLastAwardPeriod($year, $month, $type);

            $url = \strtr($this->historyUrl, [
                '@type'        => $type,
                '@year'        => $year,
                '@awardPeriod' => $awardPeriod,
            ]);

            $key = \strtr($cacheKey, ['@url' => $url]);
            if (Cache::has($key)) {
                continue;
            }

            $result = Http::get($url, [
                'dt' => $this->getFormatDate(),
            ]);

            $result = \json_decode(\trim($result->body(), 'jsonString='), true);

            $drawresult = Arr::get($result, 'drawresult');

            foreach ($drawresult as $item) {
                BjdcResult::query()->firstOrCreate([
                    'award_period' => $awardPeriod,
                    'matchno'      => $item['matchno'],
                    'type'         => $type,
                ], [
                    'result'   => $item['result'],
                    'sp_value' => $item['spvalue'],
                    'results'  => $item,
                    'sync'     => 0,
                ]);
            }

            Cache::forever($key, 1);
        }
    }

    // 获取历史奖期
    public function historyLastAwardPeriod(string $year, $month, string $type)
    {
        $url = \strtr($this->historyLastAwardPeriodUrl, [
            '@year'  => $year,
            '@type'  => $type,
            '@month' => $month,
        ]);

        $result = Http::get($url, [
            'dt' => $this->getFormatDate(),
        ]);

        $result = \json_decode(\trim($result->body(), 'jsonString='), true);

        return \max(\array_column($result['drawnolist'], 'drawno'));

    }

    // 获取历史月份
    public function historyMonth(string $year, string $type)
    {
        $url = \strtr($this->historyMonthUrl, [
            '@year' => $year,
            '@type' => $type,
        ]);

        $result = Http::get($url, [
            'dt' => $this->getFormatDate(),
        ]);

        $result = \json_decode(\trim($result->body(), 'jsonString='), true);

        return \max(\array_column($result['monthlist'], 'month'));

    }

    //获取历史年份
    public function historyYear(string $type)
    {
        $url = \strtr($this->historyYearUrl, [
            '@type' => $type,
        ]);

        $result = Http::get($url, [
            'dt' => $this->getFormatDate(),
        ]);


        $result = \json_decode(\trim($result->body(), 'jsonString='), true);
        return \max(\array_column($result['drawyears'], 'year'));
    }


    public function parlayGetGame(string $draw, string $type)
    {
        $url = \strtr($this->parlayGetGame, [
            '@type' => $type,
            '@draw' => $draw,
        ]);


        $result = Http::get($url, [
            'dt' => $this->getFormatDate(),
            "_"  => $this->getMillisecond(),
        ]);

        if ($result->status() != 200) {
            return;
        }

        $xml  = \simplexml_load_string($result->body());
        $json = \json_encode($xml);

        $arr = \json_decode($json, true);

        $data = Arr::get($arr, 'matchesp');


        if (isset($data['matchInfo']['matchelem'])) {
            foreach ($data['matchInfo']['matchelem'] as $matchelem) {

                foreach ($matchelem as $item) {

                    $this->parlayGetGameInstall($item, $type, $draw);
                }
            }
        } else {
            foreach ($data['matchInfo'] as $item1) {

                foreach ($item1['matchelem']['item'] as $item) {
                    if (\is_array($item)){
                        $this->parlayGetGameInstall($item, $type, $draw);
                    } else {
                        $this->parlayGetGameInstall($item1['matchelem']['item'], $type, $draw);
                    }

                }

            }
        }


    }

    public function parlayGetGameInstall(array $item, string $type, string $draw)
    {
        if (($item['drawed'] ?? '') != 'True') {
            return;
        }

        list('sp_value' => $sp, 'key' => $key) = $this->formatSpItem($item['spitem'], $type);

        BjdcResult::query()->firstOrCreate([
            'award_period' => $draw,
            'matchno'      => $item['no'],
            'type'         => $type,
        ], [
            'result'   => $key,
            'sp_value' => $sp,
            'results'  => $item,
            'sync'     => 0,
        ]);
    }


    public function gameinfo_his(string|null $type)
    {
        $url = \strtr($this->gameinfo_his, [
            '@type' => $type,
        ]);

        $result = Http::get($url, [
            'dt' => $this->getFormatDate(),
            "_"  => $this->getMillisecond(),
        ]);


        return trim(\max(\explode(',', $result->body())), '﻿');
    }


    public function getFormatDate(): string
    {
        return \date('D M d Y H:i:s') . ' GMT 0800 (中国标准时间)';
    }

    public function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (int)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    public function format200(): array
    {
        return [
            'sp1' => '3',
            'sp2' => '1',
            'sp3' => '0',
        ];
    }

    public function format250(): array
    {
        return [
            'sp10' => '胜其它',
            'sp1'  => '1:0',
            'sp2'  => '2:0',
            'sp3'  => '2:1',
            'sp4'  => '3:0',
            'sp5'  => '3:1',
            'sp6'  => '3:2',
            'sp7'  => '4:0',
            'sp8'  => '4:1',
            'sp9'  => '4:2',
            'sp15' => '平其它',
            'sp11' => '0:0',
            'sp12' => '1:1',
            'sp13' => '2:2',
            'sp14' => '3:3',
            'sp25' => '负其它',
            'sp16' => '0:1',
            'sp17' => '0:2',
            'sp18' => '1:2',
            'sp19' => '0:3',
            'sp20' => '1:3',
            'sp21' => '2:3',
            'sp22' => '0:4',
            'sp23' => '1:4',
            'sp24' => '2:4',
        ];
    }

    public function format230(): array
    {
        return [
            'sp1' => '0',
            'sp2' => '1',
            'sp3' => '2',
            'sp4' => '3',
            'sp5' => '4',
            'sp6' => '5',
            'sp7' => '6',
            'sp8' => '7+',
        ];
    }

    public function format240(): array
    {
        return array(
            'sp1' => '3-3',
            'sp2' => '3-1',
            'sp3' => '3-0',
            'sp4' => '1-3',
            'sp5' => '1-1',
            'sp6' => '1-0',
            'sp7' => '0-3',
            'sp8' => '0-1',
            'sp9' => '0-0',
        );
    }

    public function format210(): array
    {
        return array(
            'sp1' => '上单',
            'sp2' => '上双',
            'sp3' => '下单',
            'sp4' => '下双',
        );

    }

    public function format270(): array
    {
        return array(
            'sp1' => '胜',
            'sp2' => '负',
        );

    }

    public function formatSpItem(array $spItem, $type): array
    {
        $sp_value = min($spItem);
        $key      = \trim(\array_search($sp_value, $spItem), '_v');

        $method   = 'format' . $type;
        $sp_value = \abs($sp_value);
        $key      = $this->$method()[$key];

        if ($sp_value == 1) {
            $key = '*';
        }

        return \compact('sp_value', 'key');
    }
}
