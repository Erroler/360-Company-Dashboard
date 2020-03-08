<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        User::create([
            'name' => 'Raul Moreira Vidal',
            'email' => 'admin@admin.com',
            'password' => \Hash::make('admin')
        ]); 
    }
}
