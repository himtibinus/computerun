<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferralCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('referral_codes')) Schema::create('referral_codes', function (Blueprint $table) {
            $table->string('id', 8)->primary();
            $table->text('name');
            $table->boolean('opened');
            $table->timestamps();
        });
        if (Schema::hasTable('registration')) Schema::table('registration', function (Blueprint $table){
            $table->string('referral_code', 8)->nullable();
            $table->foreign('referral_code')->references('id')->on('referral_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('registration')){
            Schema::table('registration', function (Blueprint $table){
                $table->dropForeign(['referral_code']);
            });
            
            Schema::table('registration', function (Blueprint $table){
                $table->dropColumn(['referral_code']);
            });
        };
        Schema::drop('referral_codes');
    }
}
