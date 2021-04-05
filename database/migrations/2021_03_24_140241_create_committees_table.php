<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommitteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('chairman')->nullable();
            $table->unsignedBigInteger('vice_chairman')->nullable();
            $table->string('members')->nullable();
            $table->foreign('chairman')->references('id')->on('bokals');
            $table->foreign('vice_chairman')->references('id')->on('bokals');
            // $table->foreign('members')->references('id')->on('bokals');
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
        Schema::dropIfExists('committees');
    }
}
