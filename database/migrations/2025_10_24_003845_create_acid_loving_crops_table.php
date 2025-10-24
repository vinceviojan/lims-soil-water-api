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
        Schema::create('acid_loving_crops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('crops', 100);
            $table->string('category_code',100);
            $table->decimal('min_ph',10,2)->nullable();
            $table->decimal('max_ph',10,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acid_loving_crops');
    }
};
