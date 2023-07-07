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
        Schema::create('bjdc_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('award_period')->default(0)->comment('奖期');
            $table->unsignedInteger('matchno')->default(0)->comment('场次');
            $table->string('result')->default('')->comment('结果');
            $table->string('sp_value')->default('')->comment('sp值');
            $table->string('type')->default('')->comment('压住类型');
            $table->json('results')->nullable()->comment('结果集');
            $table->boolean('sync')->default(0)->comment('0:没有同步 1:同步成功')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['award_period', 'matchno', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bjdc_result');
    }
};
