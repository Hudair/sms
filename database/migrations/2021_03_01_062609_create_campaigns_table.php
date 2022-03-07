<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->unsignedBigInteger('user_id');
            $table->text('campaign_name')->nullable();
            $table->longText('message')->nullable();
            $table->longText('media_url')->nullable();
            $table->string('language', 20)->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('sms_type', 15);
            $table->string('upload_type', 50)->default('normal');
            $table->string('status')->nullable();
            $table->text('reason')->nullable();
            $table->string('api_key')->nullable();
            $table->text('cache')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamp('schedule_time')->nullable();
            $table->enum('schedule_type', ['onetime', 'recurring'])->nullable();
            $table->string('frequency_cycle', 50)->nullable();
            $table->integer('frequency_amount')->nullable();
            $table->string('frequency_unit', 8)->nullable();
            $table->timestamp('recurring_end')->nullable();
            $table->timestamp('run_at')->nullable();
            $table->timestamp('delivery_at')->nullable();

            $table->string('batch_id')->nullable();

            $table->timestamps();

            // foreign
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
        Schema::dropIfExists('campaigns');
    }
}
