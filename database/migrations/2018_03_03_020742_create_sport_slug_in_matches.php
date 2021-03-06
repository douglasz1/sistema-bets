<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSportSlugInMatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('sport_slug')
                ->default('soccer')
                ->after('sport_id');
        });

        Schema::table('results', function (Blueprint $table) {
            $table->string('sport_slug')
                ->default('soccer')
                ->after('sport_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('sport_slug');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('sport_slug');
        });
    }
}
