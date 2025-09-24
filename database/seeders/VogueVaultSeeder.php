<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VogueVaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@voguevault.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1-555-0123',
            'address' => '123 Fashion Ave, Style City, SC 12345',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create sample users
        DB::table('users')->insertOrIgnore([
            [
                'name' => 'John Customer',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Create categories
        DB::table('categories')->insert([
            ['name' => "Women's Clothing", 'slug' => 'womens-clothing', 'description' => 'Trendy clothing for women', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Men's Clothing", 'slug' => 'mens-clothing', 'description' => 'Fashion clothing for men', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Stylish accessories', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create sample products
        DB::table('products')->insert([
            [
                'name' => 'Summer Dress',
                'slug' => 'summer-dress',
                'description' => 'Beautiful summer dress',
                'short_description' => 'Summer dress',
                'price' => 89.99,
                'sku' => 'SD001',
                'stock' => 25,
                'category_id' => 1,
                'images' => json_encode(['dress.jpg']),
                'attributes' => json_encode(['sizes' => ['S', 'M', 'L']]),
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
