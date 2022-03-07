<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansSendingServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans_sending_servers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sending_server_id');
            $table->unsignedBigInteger('plan_id');
            $table->integer('fitness');
            $table->tinyInteger('is_primary')->default(false);

            $table->timestamps();

            $table->foreign('sending_server_id')->references('id')->on('sending_servers')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans_sending_servers');
    }
}
