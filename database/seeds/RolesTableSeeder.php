<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        Role::create([
            'name' => 'User',
            'slug' => 'user',
        ]);

        Role::create([
            'name' => 'Buyer',
            'slug' => 'buyer',
        ]);

        Role::create([
            'name' => 'Secretary',
            'slug' => 'secretary',
        ]);
    }
}
