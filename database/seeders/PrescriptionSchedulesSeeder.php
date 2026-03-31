<?php

namespace Database\Seeders;

use App\Models\Prescription;
use App\Models\PrescriptionSchedule;
use Illuminate\Database\Seeder;

class PrescriptionSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prescription::query()->each(function (Prescription $prescription): void {
            PrescriptionSchedule::factory()
                ->count(rand(2, 4))
                ->create([
                    'prescription_id' => $prescription->id,
                ]);
        });
    }
}
