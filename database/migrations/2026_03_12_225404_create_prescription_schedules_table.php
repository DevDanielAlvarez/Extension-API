<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prescription_schedules', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('prescription_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week');
            $table->time('time');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_schedules');
    }
};
