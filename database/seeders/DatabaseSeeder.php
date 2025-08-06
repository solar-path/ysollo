<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
        ]);

        // Tenant and domain 
        $tenant = Tenant::query()->create([
            'id' => 'trebble',
            'workspace_name' => 'Trebble Workspace',
            'slug' => 'trebble',
            'owner_id' => $user->id,
        ]);

        $tenant->domains()->create([
            'domain' => 'trebble.ysollo.test',
        ]);
        
        $tenant->users()->attach($user->id, [
            'role' => 'owner',
            'status' => 'active',
            'joined_at' => now(),
        ]);
    }
}
