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
        Schema::create('msl_interpretations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('symbol', 100);
            $table->string('min', 100);
            $table->string('max', 100);
            $table->string('interpretation',100);
            $table->string('units',100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msl_interpretations');
    }
};
