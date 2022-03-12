<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsFieldToPlansCoverageCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans_coverage_countries', function (Blueprint $table) {
            $table->uuid('uid');
            $table->boolean('status')->default(true);
            $table->text('options')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans_coverage_countries', function (Blueprint $table) {
            $table->dropColumn('uid');
            $table->dropColumn('status');
            $table->dropColumn('options');
        });
    }
}
