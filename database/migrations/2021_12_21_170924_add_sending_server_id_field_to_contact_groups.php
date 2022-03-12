<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendingServerIdFieldToContactGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('sending_server')->nullable();

            // foreign
            $table->foreign('sending_server')->references('id')->on('sending_servers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            $table->dropForeign(config('database.connections.mysql.prefix').'contact_groups_sending_server_foreign');
            $table->dropColumn('sending_server');
        });
    }
}
