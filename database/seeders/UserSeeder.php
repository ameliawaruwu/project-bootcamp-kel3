<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create Merchant Users (one for each merchant)
        $merchantAccounts = [
            [
                'merchant_id' => 1,
                'name' => 'Sate Solo Pak Komar Admin',
                'email' => 'satesolo@example.com',
                'address' => 'Jalan Sukapura No.76, Cipagalo, Kec. Bojongsoang, Kabupaten Bandung',
                'latitude' => -6.968525,
                'longitude' => 107.637171,
            ],
            [
                'merchant_id' => 2,
                'name' => 'Bakso Neng Amor 3 Admin',
                'email' => 'baksoamor@example.com',
                'address' => 'Jl. Pungkur No.95j, Pungkur, Kec.Regol, Kota Bandung',
                'latitude' => -6.927291,
                'longitude' => 107.607725,
            ],
            [
                'merchant_id' => 3,
                'name' => 'Geprek Maniak Admin',
                'email' => 'geprekmaniak@example.com',
                'address' => 'Jl.Cibadak No.295, Jamika, Kec.Bojongloa Kaler, Kota Bandung',
                'latitude' => -6.920069,
                'longitude' => 107.592653,
            ],
            [
                'merchant_id' => 4,
                'name' => 'Nasi Goreng Aceng Admin',
                'email' => 'nasigoreng@example.com',
                'address' => 'Jl. Cibadak No.126, Cibadak, Kec. Astanaanyar, Kota Bandung',
                'latitude' => -6.920823,
                'longitude' => 107.596413,
            ],
            [
                'merchant_id' => 5,
                'name' => 'KetopraK Mas No Admin',
                'email' => 'ketoprakmasno@example.com',
                'address' => 'Jl. Mangga Dua No.25, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
                'latitude' => -6.1867,
                'longitude' => 106.8286,
            ],
        ];

        foreach ($merchantAccounts as $account) {
            User::create([
                'merchant_id' => $account['merchant_id'],
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make('password'),
                'role' => 'merchant',
                'address' => $account['address'],
                'latitude' => $account['latitude'],
                'longitude' => $account['longitude'],
            ]);
        }

        // Create Customer Users
        $customerAccounts = [
            [
                'name' => 'Isna Rumaisa',
                'email' => 'customer1@example.com',
                'address' => 'Jl. Podomoro Boulevard Utara No.1, Lengkong, Kec.Bojongsoang, Kabupaten Bandung',
                'latitude' => -6.975803,
                'longitude' => 107.636686,
            ],
            [
                'name' => 'Felix',
                'email' => 'customer2@example.com',
                'address' => 'Jl. Sukapura No.26, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
                'latitude' => -6.967763,
                'longitude' => 107.634909,
            ],
            [
                'name' => 'Kolab',
                'email' => 'customer3@example.com',
                'address' => 'Jl. Telekomunikasi No.1, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
                'latitude' => -6.973294,
                'longitude' => 107.632601,
            ],
            [
                'name' => 'Adinda',
                'email' => 'customer4@example.com',
                'address' => 'Jl. Buah Batu Regency, Kujangsary, Kec.Bandung Kidul, Kota Bandung',
                'latitude' => -6.965280,
                'longitude' => 107.641620,
            ],
            [
                'name' => 'Haryadi',
                'email' => 'customer5@example.com',
                'address' => 'Jl. Buah Batu Regency, Kujangsary, Kec.Bandung Kidul, Kota Bandung',
                'latitude' => -6.965280,
                'longitude' => 107.641620,
            ],
        ];

        foreach ($customerAccounts as $account) {
            User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => $account['address'],
                'latitude' => $account['latitude'],
                'longitude' => $account['longitude'],
            ]);
        }
    }
}
