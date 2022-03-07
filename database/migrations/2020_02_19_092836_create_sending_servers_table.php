<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendingServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sending_servers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('settings');
            $table->longText('api_link')->nullable();
            $table->string('port', 20)->nullable();
            $table->longText('username')->nullable();
            $table->longText('password')->nullable();
            $table->string('route')->nullable();
            $table->string('sms_type')->nullable(); //promotional or transactional
            $table->string('account_sid')->nullable(); //twilio, zang, xoxzo, ytel, apifonica
            $table->string('auth_id')->nullable(); // Plivo, PlivoPowerpack, KarixIO
            $table->string('auth_token')->nullable();
            $table->string('access_key')->nullable(); // Messagebird, AmazonSNS, FlowRoute
            $table->string('secret_access')->nullable(); // AmazonSNS
            $table->string('access_token')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('user_token')->nullable(); //semysms, tropo
            $table->string('project_id')->nullable(); //signalwire
            $table->string('api_token')->nullable();
            $table->string('auth_key')->nullable();
            $table->string('device_id')->nullable();
            $table->string('region')->nullable();
            $table->string('application_id')->nullable();
            $table->string('source_addr_ton')->default(5);
            $table->string('source_addr_npi')->default(0);
            $table->string('dest_addr_ton')->default(1);
            $table->string('dest_addr_npi')->default(1);
            $table->string('c1')->nullable();
            $table->string('c2')->nullable();
            $table->string('c3')->nullable();
            $table->string('c4')->nullable();
            $table->string('c5')->nullable();
            $table->string('c6')->nullable();
            $table->string('c7')->nullable();
            $table->enum('type', ['http', 'smpp', 'whatsapp'])->default('http');
            $table->boolean('status')->default(true);
            $table->boolean('plain')->default(false);
            $table->boolean('schedule')->default(false);
            $table->boolean('two_way')->default(false);
            $table->boolean('voice')->default(false);
            $table->boolean('mms')->default(false);
            $table->boolean('whatsapp')->default(false);
            $table->integer('sms_per_request')->default(1);
            $table->integer('quota_value')->default(0);
            $table->integer('quota_base')->default(0);
            $table->string('quota_unit', 50)->default('minute');
            $table->boolean('custom')->default(false);
            $table->integer('custom_order')->default(0);
            $table->string('success_keyword')->nullable();
            $table->timestamps();

            //foreign
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sending_servers');
    }

}
