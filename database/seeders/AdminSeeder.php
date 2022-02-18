<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\User;
use Spatie\Permission\Models\Role;
use Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminExist = User::where('email','developer@codingcafe.website')->exists();
        if ($adminExist != 1) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'developer@codingcafe.website',
                'password' => Hash::make('secret')
            ]);
            $user->assignRole('admin');            
        }
    }
}
