<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunicationStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('communication');
            $table->foreign('communication')->references('id')->on('for_referrals');
            $table->tinyInteger('endorsement')->nullable();
            $table->tinyInteger('committee_report')->nullable();
            $table->tinyInteger('second_reading')->nullable();
            $table->tinyInteger('third_reading')->nullable();
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
        Schema::dropIfExists('communication_status');
    }
}
