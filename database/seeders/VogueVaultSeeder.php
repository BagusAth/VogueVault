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
            // Women's Clothing (category_id = 1)
            [
                'name' => 'Elegant Summer Dress',
                'description' => 'A beautiful flowing summer dress perfect for any occasion. Made with breathable cotton blend fabric that keeps you cool and comfortable all day long.',
                'short_description' => 'Elegant flowing summer dress',
                'price' => 89.99,
                'stock' => 25,
                'category_id' => 1,
                'images' => json_encode(['women/dress1.jpg', 'women/dress1-back.jpg', 'women/dress1-detail.jpg']),
                'attributes' => json_encode(['sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'colors' => ['Blue', 'Red', 'Black'], 'material' => 'Cotton Blend']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Classic White Blouse',
                'description' => 'Timeless white blouse that pairs perfectly with any outfit. Professional yet stylish, made from premium cotton fabric with a comfortable fit.',
                'short_description' => 'Timeless white professional blouse',
                'price' => 49.99,
                'stock' => 40,
                'category_id' => 1,
                'images' => json_encode(['women/blouse1.jpg', 'women/blouse1-detail.jpg', 'women/blouse1-back.jpg']),
                'attributes' => json_encode(['sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'colors' => ['White', 'Cream'], 'material' => 'Cotton']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'High-Waisted Skinny Jeans',
                'description' => 'Premium denim jeans with high-waisted design and skinny fit. Perfect for everyday wear, made with stretch denim for comfort and style.',
                'short_description' => 'High-waisted premium denim jeans',
                'price' => 79.99,
                'stock' => 30,
                'category_id' => 1,
                'images' => json_encode(['women/jeans1.jpg', 'women/jeans1-back.jpg', 'women/jeans1-detail.jpg']),
                'attributes' => json_encode(['sizes' => ['24', '26', '28', '30', '32'], 'colors' => ['Dark Blue', 'Light Blue', 'Black'], 'material' => 'Stretch Denim']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Floral Midi Skirt',
                'description' => 'Beautiful floral print midi skirt with pleated design. Perfect for spring and summer occasions, featuring a comfortable elastic waistband.',
                'short_description' => 'Floral print pleated midi skirt',
                'price' => 59.99,
                'stock' => 20,
                'category_id' => 1,
                'images' => json_encode(['women/skirt1.jpg', 'women/skirt1-detail.jpg', 'women/skirt1-back.jpg']),
                'attributes' => json_encode(['sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'colors' => ['Pink Floral', 'Blue Floral', 'Yellow Floral'], 'material' => 'Polyester']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cozy Knit Sweater',
                'description' => 'Soft and warm knit sweater perfect for cooler weather. Made with premium wool blend for ultimate comfort and warmth.',
                'short_description' => 'Soft wool blend knit sweater',
                'price' => 69.99,
                'stock' => 35,
                'category_id' => 1,
                'images' => json_encode(['women/sweater1.jpg', 'women/sweater1-back.jpg', 'women/sweater1-detail.jpg']),
                'attributes' => json_encode(['sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'colors' => ['Beige', 'Gray', 'Navy', 'Burgundy'], 'material' => 'Wool Blend']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Men's Clothing (category_id = 2)
            [
                'name' => 'Classic Polo Shirt',
                'description' => 'Premium cotton polo shirt with classic fit. Perfect for casual and semi-formal occasions, featuring a comfortable collar and button placket.',
                'short_description' => 'Premium cotton classic polo shirt',
                'price' => 39.99,
                'stock' => 50,
                'category_id' => 2,
                'images' => json_encode(['men/polo1.jpg', 'men/polo1-detail.jpg', 'men/polo1-back.jpg']),
                'attributes' => json_encode(['sizes' => ['S', 'M', 'L', 'XL', 'XXL'], 'colors' => ['Navy', 'White', 'Gray', 'Green'], 'material' => 'Cotton']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Formal Dress Shirt',
                'description' => 'Crisp white dress shirt perfect for business and formal events. Non-iron fabric for easy care, featuring a classic collar and French cuffs.',
                'short_description' => 'Non-iron white formal dress shirt',
                'price' => 59.99,
                'stock' => 40,
                'category_id' => 2,
                'images' => json_encode(['men/dress-shirt1.jpg', 'men/dress-shirt1-detail.jpg', 'men/dress-shirt1-cuffs.jpg']),
                'attributes' => json_encode(['sizes' => ['14.5', '15', '15.5', '16', '16.5', '17'], 'colors' => ['White', 'Light Blue'], 'material' => 'Cotton Blend']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Slim Fit Chinos',
                'description' => 'Modern slim fit chino pants made from premium cotton twill. Versatile for work and weekend, featuring a comfortable mid-rise fit.',
                'short_description' => 'Slim fit premium cotton chinos',
                'price' => 69.99,
                'stock' => 45,
                'category_id' => 2,
                'images' => json_encode(['men/chinos1.jpg', 'men/chinos1-back.jpg', 'men/chinos1-detail.jpg']),
                'attributes' => json_encode(['sizes' => ['30', '32', '34', '36', '38'], 'colors' => ['Khaki', 'Navy', 'Gray', 'Olive'], 'material' => 'Cotton Twill']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casual Hoodie',
                'description' => 'Comfortable fleece hoodie perfect for casual wear. Soft interior lining and kangaroo pocket, ideal for relaxed days and workouts.',
                'short_description' => 'Comfortable fleece casual hoodie',
                'price' => 54.99,
                'stock' => 38,
                'category_id' => 2,
                'images' => json_encode(['men/hoodie1.jpg', 'men/hoodie1-back.jpg', 'men/hoodie1-detail.jpg']),
                'attributes' => json_encode(['sizes' => ['S', 'M', 'L', 'XL', 'XXL'], 'colors' => ['Gray', 'Black', 'Navy', 'Maroon'], 'material' => 'Cotton Fleece']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Denim Jacket',
                'description' => 'Classic denim jacket with vintage wash. Timeless style that goes with everything, featuring chest pockets and button closure.',
                'short_description' => 'Classic vintage wash denim jacket',
                'price' => 89.99,
                'stock' => 25,
                'category_id' => 2,
                'images' => json_encode(['men/denim-jacket1.jpg', 'men/denim-jacket1-back.jpg', 'men/denim-jacket1-detail.jpg']),
                'attributes' => json_encode(['sizes' => ['S', 'M', 'L', 'XL', 'XXL'], 'colors' => ['Light Blue', 'Dark Blue', 'Black'], 'material' => 'Denim']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Accessories (category_id = 3)
            [
                'name' => 'Leather Wrist Watch',
                'description' => 'Elegant leather watch with stainless steel case. Water-resistant and perfect for any occasion, featuring a classic analog display.',
                'short_description' => 'Elegant leather stainless steel watch',
                'price' => 199.99,
                'stock' => 20,
                'category_id' => 3,
                'images' => json_encode(['accessories/watch1.jpg', 'accessories/watch1-detail.jpg', 'accessories/watch1-face.jpg']),
                'attributes' => json_encode(['colors' => ['Black Leather', 'Brown Leather'], 'material' => 'Leather & Steel', 'water_resistant' => true]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Designer Sunglasses',
                'description' => 'Premium UV protection sunglasses with polarized lenses. Stylish frame design that complements any face shape and outfit.',
                'short_description' => 'Premium UV protection designer sunglasses',
                'price' => 129.99,
                'stock' => 30,
                'category_id' => 3,
                'images' => json_encode(['accessories/sunglasses1.jpg', 'accessories/sunglasses1-case.jpg', 'accessories/sunglasses1-detail.jpg']),
                'attributes' => json_encode(['colors' => ['Black', 'Tortoiseshell', 'Silver'], 'uv_protection' => 'UV400', 'polarized' => true]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silk Scarf',
                'description' => 'Luxurious 100% silk scarf with beautiful print design. Perfect accessory for any outfit, can be worn multiple ways for versatile styling.',
                'short_description' => 'Luxurious 100% silk printed scarf',
                'price' => 79.99,
                'stock' => 25,
                'category_id' => 3,
                'images' => json_encode(['accessories/scarf1.jpg', 'accessories/scarf1-detail.jpg', 'accessories/scarf1-styled.jpg']),
                'attributes' => json_encode(['colors' => ['Floral Blue', 'Geometric Red', 'Abstract Gold'], 'material' => '100% Silk']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leather Belt',
                'description' => 'Genuine leather belt with classic buckle design. Perfect for both casual and formal wear, made from high-quality Italian leather.',
                'short_description' => 'Genuine leather classic belt',
                'price' => 45.99,
                'stock' => 40,
                'category_id' => 3,
                'images' => json_encode(['accessories/belt1.jpg', 'accessories/belt1-buckle.jpg', 'accessories/belt1-detail.jpg']),
                'attributes' => json_encode(['sizes' => ['32', '34', '36', '38', '40'], 'colors' => ['Black', 'Brown', 'Tan'], 'material' => 'Genuine Leather']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Statement Necklace',
                'description' => 'Bold statement necklace with gold-plated finish. Perfect for special occasions and evening wear, featuring an eye-catching design.',
                'short_description' => 'Bold gold-plated statement necklace',
                'price' => 89.99,
                'stock' => 15,
                'category_id' => 3,
                'images' => json_encode(['accessories/necklace1.jpg', 'accessories/necklace1-detail.jpg', 'accessories/necklace1-styled.jpg']),
                'attributes' => json_encode(['colors' => ['Gold', 'Silver', 'Rose Gold'], 'material' => 'Gold Plated', 'chain_length' => '18 inches']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
