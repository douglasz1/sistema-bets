<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtendCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('print_name')->after('name');
            $table->tinyInteger('quotation_modifier')->default(0)->after('print_name');
            $table->mediumInteger('max_prize')->default(12000)->unsigned()->after('quotation_modifier');
            $table->smallInteger('max_prize_multiplier')->default(1000)->unsigned()->after('max_prize');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('print_name');
            $table->dropColumn('quotation_modifier');
            $table->dropColumn('max_prize');
            $table->dropColumn('max_prize_multiplier');
        });
    }
}
