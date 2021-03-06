<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MudarCampoLeagueIdDasLigas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign('matches_league_id_foreign');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign('results_league_id_foreign');
        });

        DB::statement("ALTER TABLE `results` CHANGE `league_id` `league_id` varchar(50) NOT NULL AFTER `match_id`;");

        Schema::table('leagues', function (Blueprint $table) {
            $table->string('league_id', 50)->change();
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->string('league_id', 50)->change();
            $table->foreign('league_id')
                ->references('league_id')
                ->on('leagues')
                ->onDelete('cascade');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->foreign('league_id')
                ->references('league_id')
                ->on('leagues')
                ->onDelete('cascade');
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->string('api_id', 50)->change();
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
            //
        });
    }
}
