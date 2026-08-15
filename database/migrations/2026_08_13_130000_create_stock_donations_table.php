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
        Schema::create('stock_donations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('stock_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('patient_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('donor_name');
            $table->string('donor_phone')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('PENDING'); // enum StockDonationStatusEnum
            $table->foreignUlid('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_donations');
    }
};
