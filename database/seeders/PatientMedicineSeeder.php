<?php

namespace Database\Seeders;

use App\Models\PatientMedicine;
use App\Models\Prescription;
use Illuminate\Database\Seeder;

class PatientMedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * One PatientMedicine balance per distinct (patient, medicine) pair
     * already prescribed — a patient can have several prescriptions for the
     * same medicine over time, but only one stock balance for it.
     */
    public function run(): void
    {
        $pairs = Prescription::query()
            ->select('patient_id', 'medicine_id')
            ->distinct()
            ->get();

        foreach ($pairs as $pair) {
            PatientMedicine::firstOrCreate(
                ['patient_id' => $pair->patient_id, 'medicine_id' => $pair->medicine_id],
                [
                    'current_quantity' => fake()->numberBetween(5, 60),
                    'minimum_quantity' => fake()->numberBetween(2, 10),
                ]
            );
        }
    }
}
