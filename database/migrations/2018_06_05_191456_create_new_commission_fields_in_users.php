<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewCommissionFieldsInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('commission6')->default(10)->after('value_max3');
            $table->decimal('value_min6')->default(2)->after('commission6');
            $table->decimal('value_max6')->default(200)->after('value_min6');

            $table->integer('commission11')->default(10)->after('value_max6');
            $table->decimal('value_min11')->default(2)->after('commission11');
            $table->decimal('value_max11')->default(200)->after('value_min11');

            $table->integer('commission16')->default(10)->after('value_max11');
            $table->decimal('value_min16')->default(2)->after('commission16');
            $table->decimal('value_max16')->default(200)->after('value_min16');
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
            $table->dropColumn('commission6');
            $table->dropColumn('value_min6');
            $table->dropColumn('value_max6');

            $table->dropColumn('commission11');
            $table->dropColumn('value_min11');
            $table->dropColumn('value_max11');

            $table->dropColumn('commission16');
            $table->dropColumn('value_min16');
            $table->dropColumn('value_max16');
        });
    }
}
