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
            $table->tinyInteger('endorsement')->default('0');
            $table->tinyInteger('committee_report')->default('0');
            $table->tinyInteger('second_reading')->default('0');
            $table->tinyInteger('third_reading')->default('0');
            $table->tinyInteger('passed')->default('0');
            $table->tinyInteger('adopt')->default('0');
            $table->tinyInteger('approved')->default('0');
            $table->tinyInteger('furnished')->default('0');
            $table->tinyInteger('published')->default('0');
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
