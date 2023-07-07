<?php

namespace App\Console\Commands;

use App\Enum\OrderType;
use App\Models\MatchResult;
use App\Models\Order;
use App\Models\Shop;
use App\Services\FeedbackService;
use App\Services\OrderService;
use App\Services\SnoopyService;
use HtmlParser\ParserDom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Sunra\PhpSimple\HtmlDomParser;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private string $getVtoolsConfigV1 = 'https://webapi.sporttery.cn/gateway/report/getVtoolsConfigV1.qry';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $str = <<<AAA
[{
        "name":"胜",
        "key":"sp1"
      },{
        "name":"负",
        "key":"sp2"
      }]
AAA;

//        $arr = \json_decode($str, true);
//
//        \dd(\var_export(\array_column(\array_merge(...$arr), """name""", "key")));


//\dd(\json_decode($str, true));
        var_export(\array_column(\json_decode($str, true),  "name", 'key'));


        \dd(12);
        define('MAX_FILE_SIZE', 6000000);
        $snoopy = new SnoopyService();

        $str = $snoopy->fetch("https://zx.500.com/zqdc/kaijiang.php?playid=0&expect=23031")->getResults();


        $html = preg_replace('/charset=gb2312/', 'charset=utf-8', $str);
        // $enc = mb_detect_encoding($html);
        $html = iconv('gbk', 'utf-8', $html);


        $parserDom = new ParserDom($html);
        $content   = $parserDom->find('div#an_container div.an_box div div.lea_list table.ld_table tbody tr');
        foreach ($content as $key => $item) {
            if ($key > 0) {
//                \dd($item->find('td', 0)->getPlainText());
                \var_dump($item->find('td', 8)->getPlainText());
                \var_dump($item->find('td', 9)->getPlainText());

                \var_dump($item->find('td', 11)->getPlainText());
                \var_dump($item->find('td', 12)->getPlainText());

                \var_dump($item->find('td', 14)->getPlainText());
                \var_dump($item->find('td', 15)->getPlainText());

                \var_dump($item->find('td', 17)->getPlainText());
                \var_dump($item->find('td', 18)->getPlainText());

                \var_dump($item->find('td', 20)->getPlainText());
                \var_dump($item->find('td', 21)->getPlainText());
                die;
            }
        }

        \dd();

//        $html = HtmlDomParser::str_get_html($str);
//
//        $ret  = $html->find("div[id=an_container]");
//
        \dd($ret);

        $spv = -\max(abs(\min($arr)), \abs(\max($arr)));
        \dd(\array_search($spv, $arr));
        \dd(\min($arr));

        OrderService::instance()->checkWinning();
//        $aa = $this->getNub(3.135);
//        \dd($aa);

//        $order = Order::query()->where('order_no', "202302031137305422")->first();
//        OrderService::instance()->runOrderIsWinning($order);
    }

}

