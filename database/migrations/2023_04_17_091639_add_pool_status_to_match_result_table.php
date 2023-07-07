<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('match_result', function (Blueprint $table) {
            $table->string('pool_status')->default('payout')->comment('payout:已完成 refund:无效场次')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('match_result', function (Blueprint $table) {
            $table->dropColumn('pool_status');
        });
    }
};
