<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedTinyInteger('api_id');
            $table->string('name');
            $table->string('slug');
            $table->boolean('active')->default(true);

            $table->timestamps();
        });

        Schema::table('leagues', function (Blueprint $table) {
            $table->unsignedInteger('sport_id')
                ->default(1)
                ->after('league_id');
            $table->foreign('sport_id')
                ->references('id')->on('sports')
                ->onDelete('cascade');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedInteger('sport_id')
                ->default(1)
                ->after('league_id');
            $table->foreign('sport_id')
                ->references('id')->on('sports')
                ->onDelete('cascade');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->unsignedInteger('sport_id')
                ->default(1)
                ->after('league_id');
            $table->foreign('sport_id')
                ->references('id')->on('sports')
                ->onDelete('cascade');
        });

        Schema::table('quotation_categories', function (Blueprint $table) {
            $table->unsignedInteger('sport_id')
                ->default(1)
                ->after('label');
            $table->foreign('sport_id')
                ->references('id')->on('sports')
                ->onDelete('cascade');
        });

        DB::table('sports')->insert([
            [
                'name' => 'Futebol', 'slug' => 'soccer', 'api_id' => 1,
            ],
            [
                'name' => 'Basquete', 'slug' => 'basketball', 'api_id' => 3,
            ],
            [
                'name' => 'Vôlei', 'slug' => 'volleyball', 'api_id' => 2,
            ],
            [
                'name' => 'Handball', 'slug' => 'handball', 'api_id' => 4,
            ],
            [
                'name' => 'Futebol Americano', 'slug' => 'rugby', 'api_id' => 5,
            ],
            [
                'name' => 'UFC/Boxe', 'slug' => 'boxing', 'api_id' => 6,
            ],
            [
                'name' => 'Fórmula 1', 'slug' => 'formula1', 'api_id' => 7,
            ],
        ]);

        DB::table('quotation_categories')->insert([
            ['name' => 'handicap', 'label' => 'Gols e meio', 'sport_id' => 1],
            ['name' => 'to_win_match', 'label' => 'Vencedor da partida', 'sport_id' => 2],
            ['name' => 'to_win_match', 'label' => 'Vencedor da partida', 'sport_id' => 3],
            ['name' => 'to_win_match', 'label' => 'Vencedor da partida', 'sport_id' => 4],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });

        Schema::table('quotation_categories', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn('sport_id');
        });

        Schema::dropIfExists('sports');
    }
}
