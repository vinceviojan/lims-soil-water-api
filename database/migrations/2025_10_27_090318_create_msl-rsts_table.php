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
        Schema::create('msl_rst', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('farm_area', 10,2)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->string('latitude',100)->nullable();
            $table->text('soil_texture');
            $table->decimal('ph',10,2)->nullable();
            $table->text('soil_ph_interpretation');
            $table->enum('n', ['Low', 'Medium', 'High']);
            $table->enum('p', ['Low', 'Medium', 'High']);
            $table->enum('k', ['Low', 'Medium', 'High']);
            $table->string('shc_number', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msl_rst');
    }
};
