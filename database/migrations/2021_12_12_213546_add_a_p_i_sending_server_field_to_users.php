<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAPISendingServerFieldToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('api_sending_server')->nullable();

            // foreign
            $table->foreign('api_sending_server')->references('id')->on('sending_servers')->onDelete('cascade');
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
            $table->dropForeign(config('database.connections.mysql.prefix').'users_api_sending_server_foreign');
            $table->dropColumn('api_sending_server');
        });
    }
}
