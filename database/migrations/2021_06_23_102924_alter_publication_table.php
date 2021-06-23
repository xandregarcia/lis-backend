<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPublicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->renameColumn('first_publication','first_from');
            $table->renameColumn('second_publication','second_from');
            $table->renameColumn('third_publication','third_from');
            $table->date('first_to')->nullable()->after('first_publication');
            $table->date('second_to')->nullable()->after('second_publication');
            $table->date('third_to')->nullable()->after('third_publication');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('publications', function (Blueprint $table) {
            //
        });
    }
}
