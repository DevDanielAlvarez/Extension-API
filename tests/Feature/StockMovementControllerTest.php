<?php

use App\Enums\StockItemCategoryEnum;
use App\Enums\StockMovementTypeEnum;
use App\Models\Patient;
use App\Models\StockItem;

describe('Stock movements (POST /api/stock-items/{stockItem}/movements)', function () {
    it('increases the balance on an IN movement', function () {
        $stockItem = StockItem::factory()->create(['current_quantity' => 10, 'requires_batch_control' => false]);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 15,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('stock_items', ['id' => $stockItem->id, 'current_quantity' => 25]);
        $this->assertDatabaseHas('stock_movements', [
            'stock_item_id' => $stockItem->id,
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 15,
        ]);
    });

    it('decreases the balance on an OUT movement', function () {
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::MEDICAL_SUPPLY,
            'current_quantity' => 10,
        ]);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 4,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('stock_items', ['id' => $stockItem->id, 'current_quantity' => 6]);
    });

    it('rejects an OUT movement that would leave a negative balance', function () {
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::MEDICAL_SUPPLY,
            'current_quantity' => 3,
        ]);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 10,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['quantity']);
        $this->assertDatabaseHas('stock_items', ['id' => $stockItem->id, 'current_quantity' => 3]);
    });

    it('sets the balance directly on an ADJUSTMENT movement', function () {
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::MEDICAL_SUPPLY,
            'current_quantity' => 40,
        ]);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::ADJUSTMENT->value,
            'quantity' => 33,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('stock_items', ['id' => $stockItem->id, 'current_quantity' => 33]);
    });

    it('requires a batch and expiry date on IN when the item requires batch control', function () {
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::MEDICAL_SUPPLY,
            'requires_batch_control' => true,
        ]);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 100,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['batch']);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 100,
            'batch' => 'LOTE-2026-01',
            'expiry_date' => now()->addYear()->toDateString(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('stock_movements', [
            'stock_item_id' => $stockItem->id,
            'batch' => 'LOTE-2026-01',
        ]);
    });

    it('requires a patient on OUT/RETURNED for resident-supply items', function () {
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::RESIDENT_SUPPLY,
            'current_quantity' => 10,
        ]);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 1,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['patient_id']);
    });

    it('does not require a patient on OUT for medical-supply items', function () {
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::MEDICAL_SUPPLY,
            'current_quantity' => 10,
        ]);

        $response = $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 1,
        ]);

        $response->assertStatus(201);
    });

    it('lists movements for a stock item', function () {
        $stockItem = StockItem::factory()->create(['requires_batch_control' => false]);
        $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::IN->value,
            'quantity' => 5,
        ])->assertStatus(201);

        $response = $this->getJson("/api/stock-items/{$stockItem->id}/movements");

        $response->assertStatus(200)->assertJsonStructure(['data', 'links', 'meta']);
        expect($response->json('data'))->toHaveCount(1);
    });
});

describe('Patient stock items (GET /api/patients/{patient}/stock-items)', function () {
    it('shows an item lent to the patient and hides it after return', function () {
        $patient = Patient::factory()->create();
        $stockItem = StockItem::factory()->create([
            'category' => StockItemCategoryEnum::RESIDENT_SUPPLY,
            'current_quantity' => 10,
        ]);

        $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::OUT->value,
            'quantity' => 1,
            'patient_id' => $patient->id,
        ])->assertStatus(201);

        $response = $this->getJson("/api/patients/{$patient->id}/stock-items");
        $response->assertStatus(200);
        $data = collect($response->json('data'));
        expect($data->firstWhere('id', $stockItem->id)['quantity_in_use'])->toBe(1);

        $this->postJson("/api/stock-items/{$stockItem->id}/movements", [
            'type' => StockMovementTypeEnum::RETURNED->value,
            'quantity' => 1,
            'patient_id' => $patient->id,
        ])->assertStatus(201);

        $response = $this->getJson("/api/patients/{$patient->id}/stock-items");
        $response->assertStatus(200);
        expect(collect($response->json('data'))->pluck('id'))->not->toContain($stockItem->id);
    });
});
