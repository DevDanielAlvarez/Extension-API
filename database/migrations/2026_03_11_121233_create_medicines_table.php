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
        Schema::create('medicines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->integer('content_quantity');
            $table->string('content_unit'); // enum
            $table->string('strength');
            $table->boolean('is_compounded')->default(false);
            $table->string('route_of_administration'); // enum
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
        Schema::dropIfExists('medicines');
    }
};
