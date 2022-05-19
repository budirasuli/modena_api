<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataSourceAndTitleCodeToFormRentalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_rental', function (Blueprint $table) {
            $table->string('data_source', 5)->default('WEB')->after('updated_at');
            $table->string('title_code', 5)->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_rental', function (Blueprint $table) {
            $table->dropColumn('data_source');
            $table->dropColumn('title_code');
        });
    }
}
