<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Stancl\Tenancy\Database\Models\Tenant;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Tenant::query()->create([
            'id' => 'test-tenant-1'
        ]);
        Tenant::query()->create([
            'id' => 'test-tenant-2'
        ]);
        Tenant::query()->create([
            'id' => 'test-tenant-3'
        ]);
    }
}
