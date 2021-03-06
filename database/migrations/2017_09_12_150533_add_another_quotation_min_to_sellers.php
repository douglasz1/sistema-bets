<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnotherQuotationMinToSellers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('two_tip_quotation_min')->default(1.01)->after('one_tip_quotation_min');
            $table->decimal('three_tip_quotation_min')->default(1.01)->after('two_tip_quotation_min');
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
            $table->dropColumn('two_tip_quotation_min');
            $table->dropColumn('three_tip_quotation_min');
        });
    }
}
