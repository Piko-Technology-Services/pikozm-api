<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('partner_applications', function (Blueprint $table) {
        $table->string('contact_person')->after('name');
    });
}

public function down()
{
    Schema::table('partner_applications', function (Blueprint $table) {
        $table->dropColumn('contact_person');
    });
}

};
