<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Wang9707\MakeTable\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class BjdcResult
 * @package App\Models
 *
 * @property int $id
 * @property int $award_period
 * @property int $matchno
 * @property string $score
 * @property string $sp_value
 * @property string $type
 * @property array $result
 * @property int $sync
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class BjdcResult extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 该表将与模型关联
     *
     * @var string
     */
    protected $table = 'bjdc_result';

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
         'results' => 'array',
    ];

    /**
     * 可以被批量赋值的属性
     *
     * @var array
     */
    protected $fillable = [
        'id', //
        'award_period', //奖期
        'matchno', //场次
        'results', //结果集
        'sp_value', //sp值
        'type', //压住类型
        'result', //结果
        'sync', //0:没有同步 1:同步成功
        'created_at', //
        'updated_at', //
        'deleted_at', //
    ];
}
