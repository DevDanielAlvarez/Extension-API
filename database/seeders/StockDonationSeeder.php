<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\StockDonation;
use App\Models\StockItem;
use Illuminate\Database\Seeder;

class StockDonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patientIds = Patient::query()->inRandomOrder()->limit(5)->pluck('id');
        $stockItemIds = StockItem::query()->inRandomOrder()->limit(5)->pluck('id');

        if ($patientIds->isEmpty() || $stockItemIds->isEmpty()) {
            return;
        }

        StockDonation::factory()
            ->count(5)
            ->state(fn () => [
                'patient_id' => $patientIds->random(),
                'stock_item_id' => $stockItemIds->random(),
            ])
            ->create();
    }
}
