<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('photo')->nullable();

            $table->tinyInteger('quotation_modifier')->default(0);
            $table->decimal('balance')->default(100);
            $table->decimal('limit')->default(1000);
            $table->mediumInteger('max_prize')->default(12000)->unsigned();
            $table->smallInteger('max_prize_multiplier')->default(1000)->unsigned();

            $table->tinyInteger('tips_min')->default(1)->unsigned();
            $table->tinyInteger('tips_max')->default(10)->unsigned();

            $table->decimal('commission1')->default(2);
            $table->decimal('value_min1')->default(5);
            $table->decimal('value_max1')->default(50);

            $table->decimal('commission2')->default(3);
            $table->decimal('value_min2')->default(5);
            $table->decimal('value_max2')->default(50);

            $table->decimal('commission3')->default(5);
            $table->decimal('value_min3')->default(5);
            $table->decimal('value_max3')->default(50);

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->boolean('active')->default(true);
            $table->string('password');
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
