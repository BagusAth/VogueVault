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
        
        // Create admin user
        DB::table('users')->insert([
            'name' => 'Admin bowo',
            'email' => 'bowo@voguevault.com',
            'password' => Hash::make('bowo123'),
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


        $localProducts = [
            [
                'name' => 'Classic White Blouse',
                'description' => 'A polished white blouse crafted from breathable cotton. Features a subtle sheen and tailored silhouette that transitions from office hours to evening plans.',
                'short_description' => 'Tailored white cotton blouse',
                'price' => 749000,
                'stock' => 35,
                'category_id' => 1,
                'images' => json_encode([
                    '/images/products/women/ClassicWhiteBlouse.png',
                ]),
                'specifications' => json_encode([
                    'Material' => '100% Cotton',
                    'Fit' => 'Regular',
                    'Care' => 'Machine wash cold',
                ]),
                'variants' => json_encode([
                    'Size' => ['XS', 'S', 'M', 'L', 'XL'],
                    'Color' => ['White', 'Ivory'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cozy Knit Sweater',
                'description' => 'Mid-weight knit sweater with ribbed trims and a relaxed shape. Soft wool blend keeps you warm without bulk, perfect for layering on cool days.',
                'short_description' => 'Warm wool-blend knit sweater',
                'price' => 1199000,
                'stock' => 28,
                'category_id' => 1,
                'images' => json_encode([
                    '/images/products/women/CozyKnitSweater.png',
                ]),
                'specifications' => json_encode([
                    'Material' => '70% Wool, 30% Acrylic',
                    'Fit' => 'Relaxed',
                    'Neckline' => 'Crew',
                ]),
                'variants' => json_encode([
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Beige', 'Charcoal'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Elegant Summer Dress',
                'description' => 'Sleeveless summer dress with a flowing A-line skirt and adjustable belt. Lightweight fabric moves with you for breezy comfort.',
                'short_description' => 'Flowing sleeveless summer dress',
                'price' => 1349000,
                'stock' => 22,
                'category_id' => 1,
                'images' => json_encode([
                    '/images/products/women/ElegantSummerDress.png',
                ]),
                'specifications' => json_encode([
                    'Material' => 'Linen Blend',
                    'Length' => 'Midi',
                    'Closure' => 'Back zipper',
                ]),
                'variants' => json_encode([
                    'Size' => ['XS', 'S', 'M', 'L'],
                    'Color' => ['Rose', 'Sky Blue', 'Sunset Orange'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Floral Midi Skirt',
                'description' => 'Pleated midi skirt with watercolor floral motif. Elastic waistband provides day-long comfort while the silky lining prevents cling.',
                'short_description' => 'Pleated floral midi skirt',
                'price' => 899000,
                'stock' => 24,
                'category_id' => 1,
                'images' => json_encode([
                    '/images/products/women/FloralMidiSkirt.png',
                ]),
                'specifications' => json_encode([
                    'Material' => 'Polyester',
                    'Length' => 'Midi',
                    'Waistband' => 'Elastic back',
                ]),
                'variants' => json_encode([
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Blush Floral', 'Seafoam Floral'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'High-Waisted Skinny Jeans',
                'description' => 'Stretch denim jeans with high-rise waist and contoured seams for a flattering profile. Fabric recovers shape after every wear.',
                'short_description' => 'High-rise stretch skinny jeans',
                'price' => 1099000,
                'stock' => 32,
                'category_id' => 1,
                'images' => json_encode([
                    '/images/products/women/High-WaistedSkinnyJeans.png',
                ]),
                'specifications' => json_encode([
                    'Material' => '92% Cotton, 6% Polyester, 2% Elastane',
                    'Rise' => 'High rise',
                    'Finish' => 'Clean hem',
                ]),
                'variants' => json_encode([
                    'Waist' => ['24', '26', '28', '30', '32'],
                    'Length' => ['30', '32'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casual Hoodie',
                'description' => 'Everyday fleece hoodie with brushed interior and roomy kangaroo pocket. Drawstring hood keeps you covered during evening runs.',
                'short_description' => 'Soft fleece casual hoodie',
                'price' => 659000,
                'stock' => 38,
                'category_id' => 2,
                'images' => json_encode([
                    '/images/products/men/CasualHoodie.png',
                ]),
                'specifications' => json_encode([
                    'Material' => 'Cotton Fleece',
                    'Fit' => 'Regular',
                    'Pocket' => 'Kangaroo',
                ]),
                'variants' => json_encode([
                    'Size' => ['M', 'L', 'XL', 'XXL'],
                    'Color' => ['Steel Grey', 'Midnight Navy'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Classic Polo Shirt',
                'description' => 'Classic cotton piqué polo with ribbed collar and contrast tipping. Breathable weave keeps you cool while the tailored cut elevates casual looks.',
                'short_description' => 'Cotton piqué polo shirt',
                'price' => 579000,
                'stock' => 42,
                'category_id' => 2,
                'images' => json_encode([
                    '/images/products/men/ClassicPoloShirt.png',
                ]),
                'specifications' => json_encode([
                    'Material' => '100% Cotton',
                    'Collar' => 'Ribbed polo collar',
                    'Buttons' => 'Two-button placket',
                ]),
                'variants' => json_encode([
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Navy', 'Forest Green', 'White'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Denim Jacket',
                'description' => 'Vintage-wash denim jacket with metal hardware and chest pockets. Durable cotton denim softens with time for a custom feel.',
                'short_description' => 'Vintage wash denim jacket',
                'price' => 1289000,
                'stock' => 27,
                'category_id' => 2,
                'images' => json_encode([
                    '/images/products/men/DenimJacket.png',
                ]),
                'specifications' => json_encode([
                    'Material' => '100% Cotton Denim',
                    'Fit' => 'Classic',
                    'Lining' => 'Unlined',
                ]),
                'variants' => json_encode([
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Indigo', 'Washed Black'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Formal Dress Shirt',
                'description' => 'Crisp dress shirt with wrinkle-resistant finish and spread collar. Convertible cuffs allow you to add cufflinks for formal events.',
                'short_description' => 'Wrinkle-resistant dress shirt',
                'price' => 859000,
                'stock' => 33,
                'category_id' => 2,
                'images' => json_encode([
                    '/images/products/men/FormalDressShirt.png',
                ]),
                'specifications' => json_encode([
                    'Material' => 'Cotton Blend',
                    'Sleeve' => 'Long sleeve',
                    'Collar' => 'Spread collar',
                ]),
                'variants' => json_encode([
                    'Neck Size' => ['15', '15.5', '16', '16.5', '17'],
                    'Fit' => ['Slim', 'Regular'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Slim Fit Chinos',
                'description' => 'Twill chinos with a modern slim cut and stretch for ease of movement. Garment-dyed for rich color that pairs with casual or polished looks.',
                'short_description' => 'Stretch twill slim chinos',
                'price' => 789000,
                'stock' => 36,
                'category_id' => 2,
                'images' => json_encode([
                    '/images/products/men/SlimFitChinos.png',
                ]),
                'specifications' => json_encode([
                    'Material' => '98% Cotton, 2% Spandex',
                    'Rise' => 'Mid rise',
                    'Pockets' => '4 pockets',
                ]),
                'variants' => json_encode([
                    'Waist' => ['30', '32', '34', '36'],
                    'Length' => ['30', '32', '34'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Designer Sunglasses',
                'description' => 'Premium acetate sunglasses with polarized lenses for reduced glare. Lightweight frame sits comfortably for all-day wear.',
                'short_description' => 'Polarized designer sunglasses',
                'price' => 1899000,
                'stock' => 40,
                'category_id' => 3,
                'images' => json_encode([
                    '/images/products/accessories/DesignerSunglasses.png',
                ]),
                'specifications' => json_encode([
                    'Frame' => 'Acetate',
                    'Lens' => 'Polarized UV400',
                    'Case' => 'Included hard case',
                ]),
                'variants' => json_encode([
                    'Color' => ['Black', 'Tortoise'],
                    'Lens Tint' => ['Grey', 'Amber'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leather Belt',
                'description' => 'Full-grain leather belt finished with a brushed metal buckle. Adjustable holes and hand-burnished edges for refined styling.',
                'short_description' => 'Full-grain leather belt',
                'price' => 459000,
                'stock' => 55,
                'category_id' => 3,
                'images' => json_encode([
                    '/images/products/accessories/LeatherBelt.png',
                ]),
                'specifications' => json_encode([
                    'Material' => 'Full-grain Leather',
                    'Width' => '3 cm',
                    'Buckle' => 'Brushed nickel',
                ]),
                'variants' => json_encode([
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Chestnut', 'Black'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leather Wrist Watch',
                'description' => 'Classic analog wrist watch featuring a stainless steel case and stitched leather strap. Minimalist dial with luminous markers.',
                'short_description' => 'Stainless steel leather watch',
                'price' => 2999000,
                'stock' => 18,
                'category_id' => 3,
                'images' => json_encode([
                    '/images/products/accessories/LeatherWristWatch.png',
                ]),
                'specifications' => json_encode([
                    'Movement' => 'Quartz',
                    'Water Resistance' => '50 meters',
                    'Band' => 'Genuine leather',
                ]),
                'variants' => json_encode([
                    'Band Color' => ['Black', 'Brown'],
                    'Case Size' => ['38mm', '42mm'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silk Scarf',
                'description' => 'Hand-finished silk scarf with vibrant botanical print. Lightweight and versatile for styling as a neckerchief or handbag accent.',
                'short_description' => 'Printed silk accessory scarf',
                'price' => 1099000,
                'stock' => 30,
                'category_id' => 3,
                'images' => json_encode([
                    '/images/products/accessories/SilkScarf.png',
                ]),
                'specifications' => json_encode([
                    'Material' => '100% Silk',
                    'Dimensions' => '70 x 70 cm',
                    'Finish' => 'Hand-rolled hem',
                ]),
                'variants' => json_encode([
                    'Pattern' => ['Floral Bloom', 'Sunset Abstract'],
                    'Colorway' => ['Emerald', 'Coral'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Statement Necklace',
                'description' => 'Bold statement necklace featuring layered chains and sculptural pendants. Adds instant polish to minimalist outfits.',
                'short_description' => 'Layered chain statement necklace',
                'price' => 999000,
                'stock' => 26,
                'category_id' => 3,
                'images' => json_encode([
                    '/images/products/accessories/StatementNecklace.png',
                ]),
                'specifications' => json_encode([
                    'Material' => 'Gold-tone Alloy',
                    'Length' => 'Adjustable 40-48 cm',
                    'Closure' => 'Lobster clasp',
                ]),
                'variants' => json_encode([
                    'Finish' => ['Gold', 'Rose Gold'],
                    'Charm Style' => ['Minimal', 'Crystal Accent'],
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($localProducts);
    }
}
