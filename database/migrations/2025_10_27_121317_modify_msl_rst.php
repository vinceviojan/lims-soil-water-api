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
        Schema::table('msl_rst', function (Blueprint $table) {
            $table->date('sampling_date')->nullable();
            $table->text('barangay')->nullable();
            $table->text('municipality');
            $table->text('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('msl_rst', function (Blueprint $table) {
            //
        });
    }
};
