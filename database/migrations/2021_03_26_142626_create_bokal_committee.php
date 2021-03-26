<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBokalCommittee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bokal_committee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bokal_id')->nullable();
            $table->foreign('bokal_id')->references('id')->on('bokals');
            $table->unsignedBigInteger('committee_id')->nullable();
            $table->foreign('committee_id')->references('id')->on('committees');
            $table->tinyInteger('chairman')->nullable();
            $table->tinyInteger('vice_chairman')->nullable();
            $table->tinyInteger('member')->nullable();
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
        Schema::dropIfExists('bokal_committee');
    }
}
