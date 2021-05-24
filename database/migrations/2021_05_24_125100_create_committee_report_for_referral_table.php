<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommitteeReportForReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('committee_report_for_referral', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('committee_report_id')->nullable();
            $table->foreign('committee_report_id')->references('id')->on('committee_reports');
            $table->unsignedBigInteger('for_referral_id')->nullable();
            $table->foreign('for_referral_id')->references('id')->on('for_referrals');
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
        Schema::dropIfExists('committee_report_for_referral');
    }
}
