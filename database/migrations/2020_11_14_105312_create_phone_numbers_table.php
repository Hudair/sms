<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhoneNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('number');
            $table->enum('status', ['available', 'assigned', 'expired'])->default('available');
            $table->string('capabilities')->nullable();
            $table->string('price', 50)->default(0);
            $table->string('billing_cycle', 50);
            $table->integer('frequency_amount');
            $table->string('frequency_unit', 5);
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
        Schema::dropIfExists('phone_numbers');
    }
}
