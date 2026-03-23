<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RestaurantUser;
use Illuminate\Support\Facades\Hash;

class RestaurantUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existingRestaurant = RestaurantUser::where('email', 'zav.r@gmail.com')->first();
        
        if ($existingRestaurant) {
            $this->command->info('Аккаунт ресторана с email zav.r@gmail.com уже существует!');
            return;
        }

        $restaurant = RestaurantUser::create([
            'name' => 'Zavtraki',
            'email' => 'zav.r@gmail.com',
            'password' => Hash::make('password'),
            'restaurant_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
