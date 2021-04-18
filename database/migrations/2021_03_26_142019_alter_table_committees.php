<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCommittees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->dropForeign('committees_chairman_foreign');
            $table->dropIndex('committees_chairman_foreign');
            $table->dropForeign('committees_vice_chairman_foreign');
            $table->dropIndex('committees_vice_chairman_foreign');
            $table->dropColumn('chairman');
            $table->dropColumn('vice_chairman');
            $table->dropColumn('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('committees', function (Blueprint $table) {
            //
        });
    }
}
