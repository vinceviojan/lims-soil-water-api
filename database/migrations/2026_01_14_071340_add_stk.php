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
            $table->enum('n_qual', ['Low', 'Medium', 'High'])->nullable();
            $table->enum('p_qual', ['Low', 'Medium', 'High'])->nullable();
            $table->enum('k_qual', ['Low', 'Medium', 'High'])->nullable();
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
