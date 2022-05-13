<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataSourceToFormServiceCenterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_service_center', function (Blueprint $table) {
            $table->string('data_source', 5)->default('WEB')->after('is_migration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_service_center', function (Blueprint $table) {
            $table->dropColumn('data_source');
        });
    }
}
