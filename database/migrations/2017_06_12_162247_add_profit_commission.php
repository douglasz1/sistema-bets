<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfitCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('profit_percentage')->default(0)->unsigned()->after('tips_max');
            $table->integer('commission1')->default(2)->unsigned()->change();
            $table->integer('commission2')->default(3)->unsigned()->change();
            $table->integer('commission3')->default(5)->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profit_percentage');
        });
    }
}
