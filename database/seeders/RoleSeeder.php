<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::all()->pluck('name')->toArray();
        if (!Role::where('name', 'super admin')->exists()) {
           Role::create(['name' => 'super admin']);
        }

        if (!Role::where('name', 'admin')->exists()) {
           Role::create(['name' => 'admin']);
        }
        
        if (!Role::where('name', 'user')->exists()) {
           Role::create(['name' => 'user']);
        }            
    }
}