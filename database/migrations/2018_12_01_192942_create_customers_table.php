<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('parent')->nullable();
            $table->text('company')->nullable();
            $table->text('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('financial_address')->nullable();
            $table->string('financial_city')->nullable();
            $table->string('financial_postcode')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('notifications')->nullable();
            $table->text('permissions')->nullable();
            $table->timestamps();

            // foreign
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
