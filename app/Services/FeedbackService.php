<?php

namespace App\Services;

use App\Models\Feedback;
use Wang9707\MakeTable\Eloquent\QueryFilter;
use Wang9707\MakeTable\Services\Service;
use function Symfony\Component\String\b;


class FeedbackService extends Service
{
    public $model = '\App\Models\Feedback';

    public function getResourceList(QueryFilter $filter, $request, array $with = [])
    {
        $num     = $request->input('num', 10);
        $orderBy = $request->input('order_by', 'id');
        $sort    = $request->input('sort', 'desc');

        $data = Feedback::with($with)->filter($filter)->orderBy($orderBy, $sort)->paginate($num);

        return [
            'list'         => $data->items(),
            'total'        => $data->total(),
            'current_page' => $data->currentPage(),
            'num'          => $num,
        ];
    }

    public function isRead($shopId): bool
    {
        return (bool)Feedback::query()->where('shop_id', $shopId)->where('reply', '!=','')->where('is_read', -1)->value('id');
    }


}
