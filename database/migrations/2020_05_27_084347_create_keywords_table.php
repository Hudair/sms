<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->text('title');
            $table->text('keyword_name');
            $table->text('sender_id')->nullable();
            $table->text('reply_text')->nullable();
            $table->text('reply_voice')->nullable();
            $table->text('reply_mms')->nullable();
            $table->enum('status', ['available', 'assigned', 'expired'])->default('available');
            $table->string('price', 50)->default(0);
            $table->string('billing_cycle', 50)->nullable();
            $table->integer('frequency_amount')->nullable();
            $table->string('frequency_unit', 8)->nullable();
            $table->date('validity_date')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();


            // foreign
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keywords');
    }
}
