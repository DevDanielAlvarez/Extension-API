<?php

namespace Database\Seeders;

use App\Enums\DocumentTypeEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'daniel',
            'document_type' => DocumentTypeEnum::CPF->value,
            'document_number' => '123',
            'password' => bcrypt('123'),
            'is_adm' => true,
        ]);
        $this->call([
            PermissionSeeder::class,
        ]);
    }
}
