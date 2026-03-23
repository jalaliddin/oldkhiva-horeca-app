<?php

namespace Database\Seeders;

use App\Models\Deposit;
use App\Models\LandingSetting;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@oldkhiva.uz',
            'password' => Hash::make('Admin@1234'),
            'role' => 'admin',
            'is_active' => true,
            'approved_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Demo client
        $client = User::create([
            'name' => 'Samarqand Tour',
            'email' => 'client@demo.com',
            'password' => Hash::make('Client@1234'),
            'role' => 'client',
            'is_active' => true,
            'company_name' => 'Samarqand Tour LLC',
            'inn' => '123456789',
            'director_name' => 'Alisher Karimov',
            'phone' => '+998901234567',
            'address' => 'Samarqand shahari, Registon 1',
            'bank_name' => 'Ipoteka Bank',
            'mfo' => '09014',
            'bank_account' => '20208000100000001234',
            'contract_agreed' => true,
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'email_verified_at' => now(),
        ]);

        Deposit::create(['client_id' => $client->id, 'balance' => 500000]);

        // Menu categories and items
        $categories = [
            [
                'name' => 'Sovuq taomlar',
                'name_uz' => 'Sovuq taomlar',
                'name_ru' => 'Холодные блюда',
                'name_en' => 'Cold dishes',
                'sort_order' => 1,
                'items' => [
                    ['name' => 'Achchiq chuchvara', 'price' => 45000, 'unit' => 'portion'],
                    ['name' => 'Vinegret salat', 'price' => 35000, 'unit' => 'portion'],
                    ['name' => 'Ko\'k salat', 'price' => 30000, 'unit' => 'portion'],
                ],
            ],
            [
                'name' => 'Issiq taomlar',
                'name_uz' => 'Issiq taomlar',
                'name_ru' => 'Горячие блюда',
                'name_en' => 'Hot dishes',
                'sort_order' => 2,
                'items' => [
                    ['name' => 'Osh (Palov)', 'price' => 65000, 'unit' => 'portion'],
                    ['name' => 'Dimlama', 'price' => 70000, 'unit' => 'portion'],
                    ['name' => 'Mastava', 'price' => 55000, 'unit' => 'portion'],
                    ['name' => 'Lagʻmon', 'price' => 60000, 'unit' => 'portion'],
                ],
            ],
            [
                'name' => 'Kebab va grill',
                'name_uz' => 'Kebab va grill',
                'name_ru' => 'Кебаб и гриль',
                'name_en' => 'Kebab & Grill',
                'sort_order' => 3,
                'items' => [
                    ['name' => 'Shashlik (qo\'y go\'shti)', 'price' => 85000, 'unit' => 'portion'],
                    ['name' => 'Lyula kebab', 'price' => 80000, 'unit' => 'portion'],
                    ['name' => 'Tovuq kebab', 'price' => 75000, 'unit' => 'portion'],
                ],
            ],
            [
                'name' => 'Ichimliklar',
                'name_uz' => 'Ichimliklar',
                'name_ru' => 'Напитки',
                'name_en' => 'Beverages',
                'sort_order' => 4,
                'items' => [
                    ['name' => 'Ko\'k choy', 'price' => 15000, 'unit' => 'portion'],
                    ['name' => 'Qora choy', 'price' => 15000, 'unit' => 'portion'],
                    ['name' => 'Kompot', 'price' => 20000, 'unit' => 'portion'],
                    ['name' => 'Mineral suv', 'price' => 12000, 'unit' => 'litr'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $items = $categoryData['items'];
            unset($categoryData['items']);

            $category = MenuCategory::create([...$categoryData, 'is_active' => true]);

            foreach ($items as $index => $itemData) {
                MenuItem::create([
                    'category_id' => $category->id,
                    'name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'unit' => $itemData['unit'],
                    'min_order' => 1,
                    'is_active' => true,
                    'sort_order' => $index,
                ]);
            }
        }

        // Services
        $services = [
            ['name' => 'Zal ijarasi', 'description' => 'Katta zal ijarasi (500 kishi)', 'price' => 500000, 'unit' => 'hour'],
            ['name' => 'Musiqa xizmati', 'description' => 'Professional DJ xizmati', 'price' => 300000, 'unit' => 'event'],
            ['name' => 'Foto/Video', 'description' => 'Professional fotograf va videograf', 'price' => 400000, 'unit' => 'event'],
            ['name' => 'Bezatish xizmati', 'description' => 'Zalni gullar va sharlar bilan bezatish', 'price' => 200000, 'unit' => 'event'],
            ['name' => 'Ofitsiant xizmati', 'description' => 'Har 10 kishi uchun 1 ofitsiant', 'price' => 100000, 'unit' => 'person'],
        ];

        foreach ($services as $service) {
            Service::create([...$service, 'is_active' => true]);
        }

        // Landing settings
        $settings = [
            ['key' => 'hero_title', 'value' => 'OldKhiva Restaurant', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'Xiva tarixining ta\'mi', 'type' => 'text'],
            ['key' => 'hero_image', 'value' => null, 'type' => 'image'],
            ['key' => 'about_text', 'value' => 'OldKhiva — O\'zbekistonning qadimiy Xiva shahrida joylashgan restoran. Biz 500 dan ortiq mehmonlarni qabul qila oladigan zamonaviy va qulay zallarga egamiz. Milliy o\'zbek taomlarini tayyorlashda an\'anaviy usullardan foydalanamiz.', 'type' => 'text'],
            ['key' => 'about_image', 'value' => null, 'type' => 'image'],
            ['key' => 'contact_phone', 'value' => '+998 (62) 375-00-00', 'type' => 'text'],
            ['key' => 'contact_email', 'value' => 'info@oldkhiva.uz', 'type' => 'text'],
            ['key' => 'contact_address', 'value' => 'Xiva, Ichan-Qala 1', 'type' => 'text'],
            ['key' => 'partnership_title', 'value' => 'Hamkor bo\'ling', 'type' => 'text'],
            ['key' => 'partnership_text', 'value' => 'Turistik firmalar uchun maxsus shartlar va chegirmalar', 'type' => 'text'],
            ['key' => 'show_menu_prices', 'value' => 'true', 'type' => 'boolean'],
            ['key' => 'gallery_images', 'value' => '[]', 'type' => 'json'],
            ['key' => 'features', 'value' => json_encode([
                ['icon' => 'mdi-food', 'title' => 'Milliy taomlar', 'text' => '100+ o\'zbek taomi'],
                ['icon' => 'mdi-account-group', 'title' => 'Katta zal', 'text' => '500 kishi sig\'adi'],
                ['icon' => 'mdi-star', 'title' => 'Premium xizmat', 'text' => 'Professional jamoa'],
            ]), 'type' => 'json'],
        ];

        foreach ($settings as $setting) {
            LandingSetting::create($setting);
        }
    }
}
