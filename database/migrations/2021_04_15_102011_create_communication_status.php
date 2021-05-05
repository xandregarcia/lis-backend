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
            $table->unsignedBigInteger('for_referral_id')->nullable();
            $table->foreign('for_referral_id')->references('id')
            ->on('for_referrals')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('endorsement')->nullable();
            $table->tinyInteger('committee_report')->nullable();
            $table->tinyInteger('second_reading')->nullable();
            $table->tinyInteger('third_reading')->nullable();
            $table->tinyInteger('passed')->nullable();
            $table->tinyInteger('approved')->nullable();
            $table->tinyInteger('furnished')->nullable();
            $table->tinyInteger('published')->nullable();
            $table->integer('type')->nullable();
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
