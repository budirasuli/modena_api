<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataSourceToProductRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_registrations', function (Blueprint $table) {
            // $table->string('data_source', 5)->default('WEB')->after('category_id');
            // $table->string('country_code', 5)->after('data_source');
            // $table->string('language_code', 5)->default('en')->after('country_code');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_registrations', function (Blueprint $table) {
            $table->dropColumn('data_source');
            $table->dropColumn('country_code');
            $table->dropColumn('language_code');

        });
    }
}
