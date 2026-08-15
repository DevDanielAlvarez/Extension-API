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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('category'); // enum StockItemCategoryEnum
            $table->string('unit'); // enum StockItemUnitEnum
            $table->integer('current_quantity')->default(0);
            $table->integer('minimum_quantity')->nullable();
            $table->boolean('requires_batch_control')->default(false);
            $table->text('additional_information')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
