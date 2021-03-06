<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('seller_id')->unsigned()->nullable();
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('cancel_id')->unsigned()->nullable();
            $table->foreign('cancel_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('client_name');
            $table->decimal('bet_value');
            $table->decimal('quotation_total')->default(1);
            $table->decimal('prize')->default(1);
            $table->decimal('commission')->default(1);
            $table->tinyInteger('tips_quantity')->unsigned();

            $table->enum('status', array('pending', 'win', 'lose', 'canceled'))
                ->default('pending');

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
        Schema::dropIfExists('bets');
    }
}
