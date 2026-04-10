<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'username' => 'sales01',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        // Products
        $products = [
            ['name' => 'Laptop ASUS VivoBook 14', 'price' => 7500000,  'stock' => 20],
            ['name' => 'Mouse Logitech M331',      'price' => 250000,   'stock' => 100],
            ['name' => 'Keyboard Mechanical RK61', 'price' => 450000,   'stock' => 50],
            ['name' => 'Monitor LG 24" FHD',       'price' => 2800000,  'stock' => 15],
            ['name' => 'Headset Sony WH-1000XM5',  'price' => 4500000,  'stock' => 30],
            ['name' => 'Webcam Logitech C920',     'price' => 1200000,  'stock' => 25],
            ['name' => 'SSD Samsung 1TB',          'price' => 1350000,  'stock' => 60],
            ['name' => 'USB Hub 7-Port',            'price' => 175000,   'stock' => 80],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Customers
        $customers = [
            [
                'name'    => 'PT Maju Bersama',
                'phone'   => '02112345678',
                'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            ],
            [
                'name'    => 'CV Teknologi Nusantara',
                'phone'   => '02287654321',
                'address' => 'Jl. Gatot Subroto No. 12, Jakarta Selatan',
            ],
            [
                'name'    => 'Budi Santoso',
                'phone'   => '08113456789',
                'address' => 'Jl. Kebon Jeruk No. 7, Jakarta Barat',
            ],
            [
                'name'    => 'Siti Rahma',
                'phone'   => '08229876543',
                'address' => 'Komplek Griya Asri Blok C-5, Depok',
            ],
            [
                'name'    => 'PT Solusi Digital',
                'phone'   => '02155566677',
                'address' => 'Gedung Menara BCA Lt. 30, Jakarta Pusat',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
