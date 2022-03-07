<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportJobHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_job_histories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('import_id')->nullable();
            $table->string('type');
            $table->string('status', 50)->default('processing');
            $table->text('options')->nullable();
            $table->string('batch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_job_histories');
    }
}
