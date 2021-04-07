<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBokalCommitteeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bokal_committee', function (Blueprint $table) {
            Schema::drop('bokal_committee');
            Schema::dropIfExists('bokal_committee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bokal_committee', function (Blueprint $table) {
            //
        });
    }
}
