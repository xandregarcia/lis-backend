<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppropriationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appropriations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('for_referral_id')->nullable();
            $table->foreign('for_referral_id')->references('id')->on('for_referrals');
            $table->string('title','1000')->nullable();
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
        Schema::dropIfExists('appropriations');
    }
}
