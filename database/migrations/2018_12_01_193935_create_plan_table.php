<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('currency_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 16, 2);
            $table->string('billing_cycle',50);
            $table->integer('frequency_amount');
            $table->string('frequency_unit',5);
            $table->text('options');
            $table->boolean('status')->default(true);
            $table->integer('custom_order');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('tax_billing_required')->default(false);

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
        Schema::dropIfExists('plans');
    }
}
