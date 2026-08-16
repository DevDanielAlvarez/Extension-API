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
        Schema::create('patient_medicine_movements', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('patient_medicine_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // enum StockMovementTypeEnum
            $table->integer('quantity');
            $table->foreignUlid('medication_administration_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->dateTime('movement_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_medicine_movements');
    }
};
