<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('api_id');
            $table->string('name');

            $table->timestamps();
        });

        DB::table('countries')->insert(['api_id' => 0, 'name' => 'Internacional']);

        Schema::table('leagues', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->default(1)->after('league_id');
            $table->foreign('country_id')
                ->references('id')->on('countries')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });

        Schema::dropIfExists('countries');
    }
}
