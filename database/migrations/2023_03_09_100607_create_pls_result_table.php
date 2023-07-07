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
        Schema::create('pls_result', function (Blueprint $table) {
            $table->id();
            $table->string('drawn_um')->default('')->comment('期号');
            $table->string('draw_result')->default('')->comment('中奖号');
            $table->decimal('stake_amount_1', 10, 2)->default(0.00)->comment('直选中奖金额');
            $table->decimal('stake_amount_2', 10, 2)->default(0.00)->comment('组三中奖金额');
            $table->decimal('stake_amount_3', 10, 2)->default(0.00)->comment('组六中奖金额');
            $table->json('result')->nullable()->comment('结果集');
            $table->boolean('sync')->default(0)->comment('0:没有同步 1:同步成功')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pls_result');
    }
};
