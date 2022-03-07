<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('phone');
            $table->string('status');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('anniversary_date')->nullable();
            $table->timestamps();


            //foreign
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('contact_groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
