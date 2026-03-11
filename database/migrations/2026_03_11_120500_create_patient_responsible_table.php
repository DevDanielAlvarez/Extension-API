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
        Schema::create('patient_responsible', function (Blueprint $table) {
            $table->foreignUlid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('responsible_id')->constrained('responsibles')->cascadeOnDelete();

            $table->primary(['patient_id', 'responsible_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_responsible');
    }
};
