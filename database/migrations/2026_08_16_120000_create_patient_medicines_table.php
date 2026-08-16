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
        Schema::create('patient_medicines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('medicine_id')->constrained()->cascadeOnDelete();
            $table->integer('current_quantity')->default(0);
            $table->integer('minimum_quantity')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['patient_id', 'medicine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_medicines');
    }
};
