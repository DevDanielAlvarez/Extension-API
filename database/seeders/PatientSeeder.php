<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Responsible;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::factory()->count(40)->create();
        $responsibleIds = Responsible::query()->pluck('id');

        $patients->each(function (Patient $patient) use ($responsibleIds): void {
            $patient->responsibles()->syncWithoutDetaching(
                $responsibleIds->random(rand(1, min(2, $responsibleIds->count())))->values()->all()
            );
        });
    }
}
