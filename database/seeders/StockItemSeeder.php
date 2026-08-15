<?php

namespace Database\Seeders;

use App\Enums\StockItemCategoryEnum;
use App\Enums\StockItemUnitEnum;
use App\Models\StockItem;
use Illuminate\Database\Seeder;

class StockItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * One catalog row per distinct item name — duplicated batches/lots belong
     * in stock_movements (batch/expiry_date), not as repeated catalog rows.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Travesseiro', 'category' => StockItemCategoryEnum::RESIDENT_SUPPLY, 'unit' => StockItemUnitEnum::UNIT, 'requires_batch_control' => false],
            ['name' => 'Edredom', 'category' => StockItemCategoryEnum::RESIDENT_SUPPLY, 'unit' => StockItemUnitEnum::UNIT, 'requires_batch_control' => false],
            ['name' => 'Lençol', 'category' => StockItemCategoryEnum::RESIDENT_SUPPLY, 'unit' => StockItemUnitEnum::PAIR, 'requires_batch_control' => false],
            ['name' => 'Toalha de banho', 'category' => StockItemCategoryEnum::RESIDENT_SUPPLY, 'unit' => StockItemUnitEnum::UNIT, 'requires_batch_control' => false],
            ['name' => 'Agulha', 'category' => StockItemCategoryEnum::MEDICAL_SUPPLY, 'unit' => StockItemUnitEnum::BOX, 'requires_batch_control' => true],
            ['name' => 'Seringa', 'category' => StockItemCategoryEnum::MEDICAL_SUPPLY, 'unit' => StockItemUnitEnum::BOX, 'requires_batch_control' => true],
            ['name' => 'Luva descartável', 'category' => StockItemCategoryEnum::MEDICAL_SUPPLY, 'unit' => StockItemUnitEnum::BOX, 'requires_batch_control' => true],
            ['name' => 'Gaze', 'category' => StockItemCategoryEnum::MEDICAL_SUPPLY, 'unit' => StockItemUnitEnum::PACK, 'requires_batch_control' => true],
            ['name' => 'Álcool 70%', 'category' => StockItemCategoryEnum::MEDICAL_SUPPLY, 'unit' => StockItemUnitEnum::ML, 'requires_batch_control' => true],
        ];

        foreach ($items as $item) {
            StockItem::firstOrCreate(
                ['name' => $item['name']],
                [
                    'category' => $item['category'],
                    'unit' => $item['unit'],
                    'current_quantity' => fake()->numberBetween(20, 150),
                    'minimum_quantity' => fake()->numberBetween(5, 15),
                    'requires_batch_control' => $item['requires_batch_control'],
                    'additional_information' => null,
                ]
            );
        }
    }
}
