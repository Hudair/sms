<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->string('from')->nullable();
            $table->string('to', 20);
            $table->longText('message')->nullable();
            $table->longText('media_url')->nullable();
            $table->string('sms_type', 15);
            $table->longText('status')->nullable();
            $table->enum('send_by', ['from', 'to', 'api'])->nullable();
            $table->string('cost')->default(1);
            $table->string('api_key')->nullable();
            $table->unsignedBigInteger('sending_server_id')->nullable();

            $table->timestamps();

            // foreign
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
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
        Schema::dropIfExists('reports');
    }
}
