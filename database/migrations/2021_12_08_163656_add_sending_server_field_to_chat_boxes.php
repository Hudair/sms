<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendingServerFieldToChatBoxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_boxes', function (Blueprint $table) {
            $table->unsignedBigInteger('sending_server_id')->nullable();

            // foreign
            $table->foreign('sending_server_id')->references('id')->on('sending_servers')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_boxes', function (Blueprint $table) {
            $table->dropForeign(config('database.connections.mysql.prefix').'chat_boxes_sending_server_id_foreign');
            $table->dropColumn('sending_server_id');
        });
    }
}
