<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            $table->string('resolution_no')->nullable();
            $table->string('subject','1000')->nullable();
            $table->unsignedBigInteger('bokal_id')->nullable();
            $table->foreign('bokal_id')->references('id')->on('bokals');
            $table->date('date_passed')->nullable();
            $table->tinyInteger('archive')->default('0');
            $table->string('file')->nullable();
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
        Schema::dropIfExists('resolutions');
    }
}
