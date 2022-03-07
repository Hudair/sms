<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomSendingServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_sending_servers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('server_id');
            $table->enum('http_request_method', ['get', 'post'])->default('get');
            $table->boolean('json_encoded_post')->default(false); //enable json encoded post: yes , no
            $table->string('content_type')->nullable(); //content type : none, application/x-www-form-urlencoded, application/json
            $table->string('content_type_accept')->nullable(); //content type accept: none, application/x-www-form-urlencoded, application/json
            $table->string('character_encoding')->nullable(); //character encoding: none, utf-8, utf-16, iso-8859-1, ucs-2be,
            $table->boolean('ssl_certificate_verification')->default(false); //Ignore SSL certificate verification: yes, no
            $table->string('authorization')->nullable(); //authorization: follow postman [No Auth, Bearer Token, Basic Auth]
            $table->enum('multi_sms_delimiter', [',',';','array'])->nullable();
            $table->string('username_param');
            $table->text('username_value');
            $table->string('password_param')->nullable();
            $table->text('password_value')->nullable();
            $table->boolean('password_status')->default(false);
            $table->string('action_param')->nullable();
            $table->text('action_value')->nullable();
            $table->boolean('action_status')->default(false);
            $table->string('source_param')->nullable();
            $table->text('source_value')->nullable();
            $table->boolean('source_status')->default(false);
            $table->string('destination_param');
            $table->string('message_param');
            $table->string('unicode_param')->nullable();
            $table->text('unicode_value')->nullable();
            $table->boolean('unicode_status')->default(false);
            $table->string('route_param')->nullable();
            $table->text('route_value')->nullable();
            $table->boolean('route_status')->default(false);
            $table->string('language_param')->nullable();
            $table->text('language_value')->nullable();
            $table->boolean('language_status')->default(false);
            $table->string('custom_one_param')->nullable();
            $table->text('custom_one_value')->nullable();
            $table->boolean('custom_one_status')->default(false);
            $table->string('custom_two_param')->nullable();
            $table->text('custom_two_value')->nullable();
            $table->boolean('custom_two_status')->default(false);
            $table->string('custom_three_param')->nullable();
            $table->text('custom_three_value')->nullable();
            $table->boolean('custom_three_status')->default(false);
            $table->timestamps();

            //foreign
            $table->foreign('server_id')->references('id')->on('sending_servers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_sending_servers');
    }

}
