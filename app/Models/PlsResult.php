<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Wang9707\MakeTable\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PlsResult
 * @package App\Models
 *
 * @property int $id
 * @property string $drawn_um
 * @property string $draw_result
 * @property float $stake_amount_1
 * @property float $stake_amount_2
 * @property float $stake_amount_3
 * @property array $result
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PlsResult extends Model
{
    use HasFactory;

    /**
     * 该表将与模型关联
     *
     * @var string
     */
    protected $table = 'pls_result';

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
    ];

    /**
     * 属性转换
     *
     * @var array
     */
    protected $casts = [
         'result' => 'array',
    ];

    /**
     * 可以被批量赋值的属性
     *
     * @var array
     */
    protected $fillable = [
        'id', //
        'drawn_um', //期号
        'draw_result', //中奖号
        'stake_amount_1', //直选中奖金额
        'stake_amount_2', //组三中奖金额
        'stake_amount_3', //组六中奖金额
        'result', //结果集
        'created_at', //
        'updated_at', //
        'sync', //
    ];
}
