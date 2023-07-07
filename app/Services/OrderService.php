<?php

namespace App\Services;

use App\Enum\OrderWinningStatus;
use App\Models\MatchResult;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Wang9707\MakeTable\Services\Service;


class OrderService extends Service
{
    public $model = '\App\Models\Order';

    /**
     * 前端创建订单
     *
     * @param array $data
     * @param int $shopId
     * @throws \Exception
     * @created_at 2022/10/19
     */
    public function frontCreateOrder(array $data, int $shopId)
    {
        $data['order_no']    = $this->getOrderNo($shopId);
        $data['create_time'] = now();

        $order = parent::create($data);

        $this->runOrderIsWinning($order);

        return $order;
    }

    /**
     * 获取订单号
     *
     * @param int $shpId
     * @return string
     * @created_at 2022/10/19
     */
    public function getOrderNo(int $shpId): string
    {
        return date("YmdHi") . $shpId . mt_rand(1000, 9999);
    }

    public function getById(int $id, $columns = ['*'], int $shopId = 0)
    {
        return Order::query()->where('shop_id', $shopId)->find($id);
    }

    public function updateById(int $id, array $arrtibute, int $shopId = 0)
    {

        try {
            $model = Order::query()->where('shop_id', $shopId)->findOrFail($id);
            $model->fill($arrtibute);
            $model->save();
            return $model;
        } catch (\Throwable $th) {
            $this->throw($th->getMessage());
        }
    }


    /**
     * 统计
     *
     * @param string|null $startTime
     * @param string|null $endTime
     * @param array $shopId
     * @return array
     * @created_at 2022/10/26
     */
    public function statistical(string|null $startTime = '', string|null $endTime = '', array $shopId = []): array
    {
        $shopId = array_filter($shopId);
        $model  = Order::query()->when($startTime, function (Builder $query) use ($startTime) {
            $query->where('created_at', '>=', $startTime);
        })->when($endTime, function (Builder $query) use ($endTime) {
            $query->where('created_at', '<=', $endTime);
        })->when(!empty($shopId), function (Builder $query) use ($shopId) {
            $query->whereIn('shop_id', $shopId);
        });


        $model1 = clone $model;
        $model2 = clone $model;
        $model3 = clone $model;
        $model4 = clone $model;

        $totalNum      = $model1->count();
        $totalAmount   = $model2->sum('bet_amount');
        $winningNum    = $model3->where('winning_status', OrderWinningStatus::WINNING)->count();
        $winningAmount = $model4->where('winning_status', OrderWinningStatus::WINNING)->sum('wining_amount');

        return [
            'total_num'      => $totalNum,
            'total_amount'   => $totalAmount,
            'winning_num'    => $winningNum,
            'winning_amount' => $winningAmount,
        ];
    }

    public function checkWinning()
    {
        Order::query()->where('winning_status', OrderWinningStatus::UNDRAWN)->chunkById(100, function ($orders) {

            /**
             * @var Order $order
             */
            foreach ($orders as $order) {
                $this->runOrderIsWinning($order);
            }
        });
    }

    /**
     * 判断订单是否中奖
     *
     * @param Order $order
     * @created_at 2022/12/3
     */
    public function runOrderIsWinning(Order $order)
    {
        $totalAmount = 0;

        $detail   = $order->detail;
        $matchIds = Arr::get($detail, 'match_ids', []);

        //押注详情
        $content = Arr::get($detail, 'detail', []);


        //中奖结果
        $result = MatchResult::query()->whereIn('match_id', $matchIds)->where('type', $order->type)->get();

        //检测用户购买所有场次是否都开奖了
        if (!$this->checkNumber($matchIds, $result)) {
            return;
        }

        foreach ($content as $item) {
            //这组中奖结果
            $arr  = [];
            $odds = 1;
            foreach ($item as $item1) {
                $goalLine = trim($item1['goalLine'], '+');
                $model    = $result->where('match_id', $item1['matchId'])->where('goal_line', $goalLine)->where('code', $item1['code'])->first();
                if (empty($model)) {
                    continue;
                }

                $arr[] = $model->combination == $item1['combination'];
                $odds  *= $item1['odds'];
            }

            // 如果有一场每种奖  直接跳过
            if (in_array(false, $arr)) {
                continue;
            }


            $totalAmount += $this->getNub(2 * $odds)  * $order->bet_multiplier;


        }

        $order->winning_status = empty($totalAmount) ? OrderWinningStatus::NOT_WON : OrderWinningStatus::WINNING;
        $order->wining_amount  = $totalAmount;
        $order->save();
    }

    /**
     *  检测用户购买所有场次是否都开奖了
     *
     * @param array $matchIds
     * @param Collection $collection
     * @return bool
     * @created_at 2022/11/22
     */
    public function checkNumber(array $matchIds, Collection $collection): bool
    {
        //有几个开奖了
        $count = $collection->groupBy('match_id')->count();

        return $count == count($matchIds);
    }

    public function getNub($odds): float
    {
        $tmp = ((int)($odds * 1000)) %10;

        switch ($tmp) {
            case 5;
                $m2 = ((int)($odds * 100)) % 2;
                if ($m2 == 0) {
                    return \floor($odds * 100) / 100;
                } else {
                    return \ceil($odds * 100) / 100;
                }
            default:
                return \round($odds, 2);
        }
    }
}
