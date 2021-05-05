<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBokalOrdinanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bokal_ordinance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ordinance_id')->nullable();
            $table->foreign('ordinance_id')->references('id')->on('ordinances');
            $table->unsignedBigInteger('bokal_id')->nullable();
            $table->foreign('bokal_id')->references('id')->on('bokals');
            $table->tinyInteger('author')->nullable();
            $table->tinyInteger('co_author')->nullable();
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
        Schema::dropIfExists('bokal_ordinance');
    }
}
