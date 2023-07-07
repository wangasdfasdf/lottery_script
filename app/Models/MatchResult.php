<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class MatchResult
 * @package App\Models
 *
 * @property int $id
 * @property int $match_id
 * @property int $pool_id
 * @property string $code
 * @property string $goal_line
 * @property string $combination
 * @property string $type
 * @property string $pool_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class MatchResult extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 该表将与模型关联
     *
     * @var string
     */
    protected $table = 'match_result';

    /**
     * 执行模型是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 日期转换
     *
     * @var string[]
     */
    protected $dates = [
         'created_at',
         'updated_at',
         'deleted_at',
    ];

    /**
     * 属性转换
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * 可以被批量赋值的属性
     *
     * @var array
     */
    protected $fillable = [
        'id', //
        'match_id', //比赛场次ID
        'pool_id', //每场比赛的不同压住方式ID
        'code', //压住方式,HHAD:让球胜平负, HAFU:半全场胜平负, CRS:比分, TTG:总进球,	HAD:胜平负
        'goal_line', //让球数
        'combination', //开奖结果
        'type', //football:足球 basketball:篮球
        'created_at', //
        'updated_at', //
        'deleted_at', //
        'pool_status', //payout:已完成 refund:无效场次
    ];
}
