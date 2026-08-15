<?php

use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use App\Models\StockItem;

describe('StockItemController', function () {
    describe('Store (POST /api/stock-items)', function () {
        it('creates a stock item with valid data', function () {
            $payload = [
                'name' => 'Travesseiro',
                'category' => StockItemCategoryEnum::RESIDENT_SUPPLY->value,
                'unit' => StockItemUnitEnum::UNIT->value,
                'current_quantity' => 20,
                'minimum_quantity' => 5,
                'requires_batch_control' => false,
                'additional_information' => 'Enxoval padrão',
            ];

            $response = $this->postJson('/api/stock-items', $payload);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'category',
                        'unit',
                        'current_quantity',
                        'minimum_quantity',
                        'requires_batch_control',
                        'additional_information',
                    ],
                ]);

            $this->assertDatabaseHas('stock_items', [
                'name' => 'Travesseiro',
                'category' => StockItemCategoryEnum::RESIDENT_SUPPLY->value,
                'current_quantity' => 20,
            ]);
        });

        it('defaults current_quantity to zero when not provided', function () {
            $response = $this->postJson('/api/stock-items', [
                'name' => 'Seringa 5ml',
                'category' => StockItemCategoryEnum::MEDICAL_SUPPLY->value,
                'unit' => StockItemUnitEnum::UNIT->value,
                'requires_batch_control' => true,
            ]);

            $response->assertStatus(201)
                ->assertJsonPath('data.current_quantity', 0);
        });

        it('fails with missing required fields', function () {
            $response = $this->postJson('/api/stock-items', [
                'name' => 'Agulha',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category', 'unit']);
        });

        it('fails with invalid enums', function () {
            $response = $this->postJson('/api/stock-items', [
                'name' => 'Agulha',
                'category' => 'INVALID',
                'unit' => 'INVALID',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category', 'unit']);
        });
    });

    describe('Read/Update', function () {
        it('lists stock items', function () {
            StockItem::factory()->count(2)->create();

            $response = $this->getJson('/api/stock-items');

            $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links', 'meta']);
        });

        it('shows one stock item', function () {
            $stockItem = StockItem::factory()->create();

            $response = $this->getJson("/api/stock-items/{$stockItem->id}");

            $response->assertStatus(200)
                ->assertJsonPath('data.id', $stockItem->id);
        });

        it('updates a stock item without changing its balance', function () {
            $stockItem = StockItem::factory()->create([
                'name' => 'Edredom',
                'category' => StockItemCategoryEnum::RESIDENT_SUPPLY,
                'current_quantity' => 15,
            ]);

            $response = $this->patchJson("/api/stock-items/{$stockItem->id}", [
                'name' => 'Edredom Solteiro',
                'category' => StockItemCategoryEnum::RESIDENT_SUPPLY->value,
                'unit' => StockItemUnitEnum::UNIT->value,
                'minimum_quantity' => 3,
                'requires_batch_control' => false,
                // attempting to smuggle a balance change through update — must be ignored
                'current_quantity' => 999,
            ]);

            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Edredom Solteiro');

            $this->assertDatabaseHas('stock_items', [
                'id' => $stockItem->id,
                'name' => 'Edredom Solteiro',
                'current_quantity' => 15,
            ]);
        });

        it('fails update with invalid enums', function () {
            $stockItem = StockItem::factory()->create();

            $response = $this->patchJson("/api/stock-items/{$stockItem->id}", [
                'name' => 'Item Atualizado',
                'category' => 'INVALID',
                'unit' => 'INVALID',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['category', 'unit']);
        });
    });

    describe('Low stock (GET /api/stock-items/low-stock)', function () {
        it('lists only items at or below the minimum quantity', function () {
            $low = StockItem::factory()->create(['current_quantity' => 2, 'minimum_quantity' => 5]);
            StockItem::factory()->create(['current_quantity' => 50, 'minimum_quantity' => 5]);
            StockItem::factory()->create(['current_quantity' => 10, 'minimum_quantity' => null]);

            $response = $this->getJson('/api/stock-items/low-stock');

            $response->assertStatus(200);
            $ids = collect($response->json('data'))->pluck('id');

            expect($ids)->toContain($low->id);
            expect($ids)->toHaveCount(1);
        });
    });

    describe('Soft delete lifecycle', function () {
        it('soft deletes, lists as trashed, restores and force deletes', function () {
            $stockItem = StockItem::factory()->create();

            $this->deleteJson("/api/stock-items/{$stockItem->id}")->assertStatus(204);
            $this->assertSoftDeleted('stock_items', ['id' => $stockItem->id]);

            $trashedResponse = $this->getJson('/api/stock-items/trashed');
            $trashedResponse->assertStatus(200);
            expect(collect($trashedResponse->json('data'))->pluck('id'))->toContain($stockItem->id);

            $this->postJson("/api/stock-items/{$stockItem->id}/restore")->assertStatus(200);
            $this->assertDatabaseHas('stock_items', ['id' => $stockItem->id, 'deleted_at' => null]);

            $this->deleteJson("/api/stock-items/{$stockItem->id}")->assertStatus(204);
            $this->deleteJson("/api/stock-items/{$stockItem->id}/force-delete")->assertStatus(204);
            $this->assertDatabaseMissing('stock_items', ['id' => $stockItem->id]);
        });
    });
});
