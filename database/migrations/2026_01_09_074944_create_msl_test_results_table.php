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
        Schema::create('msl_test_results', function (Blueprint $table) {
            $table->id('id');
            $table->string('longitude', 100)->nullable();
            $table->string('latitude',100)->nullable();
            $table->decimal('farm_area', 10,2)->nullable();
            $table->decimal('ph',10,2)->nullable();
            $table->decimal('om',10,2)->nullable();
            $table->decimal('p_bray',10,2)->nullable();
            $table->decimal('p_olsen',10,2)->nullable();
            $table->decimal('k',10,2)->nullable();
            $table->string('shc_number', 100);
            $table->text('soil_texture')->nullable();
            $table->text('soil_ph_interpretation')->nullable();
            $table->text('barangay')->nullable();
            $table->text('municipality')->nullable();
            $table->text('province')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msl_test_results');
    }
};
