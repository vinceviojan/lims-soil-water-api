<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('msl_test_results', function (Blueprint $table) {
            $table->string('om',100)->nullable()->change();
            $table->string('p_bray',100)->nullable()->change();
            $table->string('p_olsen',100)->nullable()->change();
            $table->string('k',100)->nullable()->change();
            $table->string('shc_number', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('msl_test_result', function (Blueprint $table) {
            //
        });
    }
};
