<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->default(0)->comment('比赛场次ID');
            $table->unsignedBigInteger('pool_id')->default(0)->comment('每场比赛的不同压住方式ID');
            $table->string('code')->default('')->comment('押注方式,足球[HHAD:让球胜平负, HAFU:半全场胜平负, CRS:比分, TTG:总进球,	HAD:胜平负], 篮球[HDC:让分胜负, HILO:大小分, MNL:胜负 WNM:胜分差] ');
            $table->string('goal_line')->default('')->comment('让球数');
            $table->string('combination')->default('')->comment('开奖结果');
            $table->string('type')->default('football')->comment('football:足球 basketball:篮球');
            $table->char('unique', 32)->default('')->comment('唯一ID')->index();
            $table->boolean('sync')->default(0)->comment('0:没有同步 1:同步成功')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_result');
    }
};
