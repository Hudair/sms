<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('customer_id');
            $table->string('name');
            $table->string('sender_id')->nullable();
            $table->boolean('send_welcome_sms')->default(true);
            $table->boolean('unsubscribe_notification')->default(true);
            $table->boolean('send_keyword_message')->default(true);
            $table->boolean('status')->default(true);
            $table->text('signup_sms')->nullable();
            $table->text('welcome_sms')->nullable();
            $table->text('unsubscribe_sms')->nullable();
            $table->text('cache')->nullable();
            $table->string('batch_id')->nullable();
            $table->timestamps();

            //foreign
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_groups');
    }
}
