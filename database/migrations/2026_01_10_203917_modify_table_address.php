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
            $table->string('barangay', 150)->nullable()->change();
            $table->string('municipality', 150)->nullable()->change();
            $table->string('province', 150)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('msl_test_results', function (Blueprint $table) {
            //
        });
    }
};
