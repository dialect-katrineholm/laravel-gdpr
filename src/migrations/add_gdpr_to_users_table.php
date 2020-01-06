<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddGdprToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('last_activity')->nullable()->default(null);
            $table->boolean('accepted_gdpr')->nullable()->default(null);
            $table->boolean('isAnonymized')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('last_activity');
            $table->dropColumn('accepted_gdpr');
            $table->dropColumn('isAnonymized');
        });
    }
}
