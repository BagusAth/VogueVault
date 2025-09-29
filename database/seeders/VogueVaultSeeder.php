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
                'name' => 'Bagus Athallah',
                'email' => 'bagus@gmail.com',
                'password' => Hash::make('bagus123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

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
            ['name' => "Women's Clothing", 'description' => 'Trendy clothing for women', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => "Men's Clothing", 'description' => 'Fashion clothing for men', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accessories', 'description' => 'Stylish accessories', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create sample products
        DB::table('products')->insert([
            // Women's Clothing (category_id = 1)
            [
                'name' => 'Elegant Summer Dress',
                'description' => 'A beautiful flowing summer dress perfect for any occasion. Made with breathable cotton blend fabric that keeps you cool and comfortable all day long.',
                'short_description' => 'Elegant flowing summer dress',
                'price' => 1399000,
                'stock' => 25,
                'category_id' => 1,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'colors' => ['Blue', 'Red', 'Black'],
                    'material' => 'Cotton Blend',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Classic White Blouse',
                'description' => 'Timeless white blouse that pairs perfectly with any outfit. Professional yet stylish, made from premium cotton fabric with a comfortable fit.',
                'short_description' => 'Timeless white professional blouse',
                'price' => 749000,
                'stock' => 40,
                'category_id' => 1,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1534452203293-494d7ddbf7e0?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'colors' => ['White', 'Cream'],
                    'material' => 'Cotton',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'High-Waisted Skinny Jeans',
                'description' => 'Premium denim jeans with high-waisted design and skinny fit. Perfect for everyday wear, made with stretch denim for comfort and style.',
                'short_description' => 'High-waisted premium denim jeans',
                'price' => 1099000,
                'stock' => 30,
                'category_id' => 1,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['24', '26', '28', '30', '32'],
                    'colors' => ['Dark Blue', 'Light Blue', 'Black'],
                    'material' => 'Stretch Denim',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Floral Midi Skirt',
                'description' => 'Beautiful floral print midi skirt with pleated design. Perfect for spring and summer occasions, featuring a comfortable elastic waistband.',
                'short_description' => 'Floral print pleated midi skirt',
                'price' => 899000,
                'stock' => 20,
                'category_id' => 1,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1525171254930-643fc658b64d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'colors' => ['Pink Floral', 'Blue Floral', 'Yellow Floral'],
                    'material' => 'Polyester',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cozy Knit Sweater',
                'description' => 'Soft and warm knit sweater perfect for cooler weather. Made with premium wool blend for ultimate comfort and warmth.',
                'short_description' => 'Soft wool blend knit sweater',
                'price' => 1199000,
                'stock' => 35,
                'category_id' => 1,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1530023367847-a683933f4177?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'colors' => ['Beige', 'Gray', 'Navy', 'Burgundy'],
                    'material' => 'Wool Blend',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Men's Clothing (category_id = 2)
            [
                'name' => 'Classic Polo Shirt',
                'description' => 'Premium cotton polo shirt with classic fit. Perfect for casual and semi-formal occasions, featuring a comfortable collar and button placket.',
                'short_description' => 'Premium cotton classic polo shirt',
                'price' => 599000,
                'stock' => 50,
                'category_id' => 2,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1520962915648-21734a932e4d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1520975918319-2be936c6f84c?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'colors' => ['Navy', 'White', 'Gray', 'Green'],
                    'material' => 'Cotton',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Formal Dress Shirt',
                'description' => 'Crisp white dress shirt perfect for business and formal events. Non-iron fabric for easy care, featuring a classic collar and French cuffs.',
                'short_description' => 'Non-iron white formal dress shirt',
                'price' => 849000,
                'stock' => 40,
                'category_id' => 2,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1528702748617-c64d49f918af?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1520962915648-21734a932e4d?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['14.5', '15', '15.5', '16', '16.5', '17'],
                    'colors' => ['White', 'Light Blue'],
                    'material' => 'Cotton Blend',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Slim Fit Chinos',
                'description' => 'Modern slim fit chino pants made from premium cotton twill. Versatile for work and weekend, featuring a comfortable mid-rise fit.',
                'short_description' => 'Slim fit premium cotton chinos',
                'price' => 799000,
                'stock' => 45,
                'category_id' => 2,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1516826431361-8937c56de1c1?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1475180098004-ca77a66827be?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1475274110913-62a7e975d1b0?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['30', '32', '34', '36', '38'],
                    'colors' => ['Khaki', 'Navy', 'Gray', 'Olive'],
                    'material' => 'Cotton Twill',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casual Hoodie',
                'description' => 'Comfortable fleece hoodie perfect for casual wear. Soft interior lining and kangaroo pocket, ideal for relaxed days and workouts.',
                'short_description' => 'Comfortable fleece casual hoodie',
                'price' => 649000,
                'stock' => 38,
                'category_id' => 2,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1487412720507-7bda0bbd9ebc?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1492447166138-50c3889fccb1?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1531251445707-1f000e1e87d0?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'colors' => ['Gray', 'Black', 'Navy', 'Maroon'],
                    'material' => 'Cotton Fleece',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Denim Jacket',
                'description' => 'Classic denim jacket with vintage wash. Timeless style that goes with everything, featuring chest pockets and button closure.',
                'short_description' => 'Classic vintage wash denim jacket',
                'price' => 1299000,
                'stock' => 25,
                'category_id' => 2,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1535295972055-1c762f4483e5?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'colors' => ['Light Blue', 'Dark Blue', 'Black'],
                    'material' => 'Denim',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Accessories (category_id = 3)
            [
                'name' => 'Leather Wrist Watch',
                'description' => 'Elegant leather watch with stainless steel case. Water-resistant and perfect for any occasion, featuring a classic analog display.',
                'short_description' => 'Elegant leather stainless steel watch',
                'price' => 2999000,
                'stock' => 20,
                'category_id' => 3,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1524592094714-0f0654e20314?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'colors' => ['Black Leather', 'Brown Leather'],
                    'material' => 'Leather & Steel',
                    'water_resistant' => true,
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Designer Sunglasses',
                'description' => 'Premium UV protection sunglasses with polarized lenses. Stylish frame design that complements any face shape and outfit.',
                'short_description' => 'Premium UV protection designer sunglasses',
                'price' => 1899000,
                'stock' => 30,
                'category_id' => 3,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1516574187841-cb9cc2ca948b?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'colors' => ['Black', 'Tortoiseshell', 'Silver'],
                    'uv_protection' => 'UV400',
                    'polarized' => true,
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silk Scarf',
                'description' => 'Luxurious 100% silk scarf with beautiful print design. Perfect accessory for any outfit, can be worn multiple ways for versatile styling.',
                'short_description' => 'Luxurious 100% silk printed scarf',
                'price' => 1099000,
                'stock' => 25,
                'category_id' => 3,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1542293787938-4d2226c12e76?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'colors' => ['Floral Blue', 'Geometric Red', 'Abstract Gold'],
                    'material' => '100% Silk',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leather Belt',
                'description' => 'Genuine leather belt with classic buckle design. Perfect for both casual and formal wear, made from high-quality Italian leather.',
                'short_description' => 'Genuine leather classic belt',
                'price' => 499000,
                'stock' => 40,
                'category_id' => 3,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1586363104866-31806e46ba5c?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1520962915648-21734a932e4d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'sizes' => ['32', '34', '36', '38', '40'],
                    'colors' => ['Black', 'Brown', 'Tan'],
                    'material' => 'Genuine Leather',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Statement Necklace',
                'description' => 'Bold statement necklace with gold-plated finish. Perfect for special occasions and evening wear, featuring an eye-catching design.',
                'short_description' => 'Bold gold-plated statement necklace',
                'price' => 1499000,
                'stock' => 15,
                'category_id' => 3,
                'images' => json_encode([
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
                ]),
                'attributes' => json_encode([
                    'colors' => ['Gold', 'Silver', 'Rose Gold'],
                    'material' => 'Gold Plated',
                    'chain_length' => '18 inches',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
