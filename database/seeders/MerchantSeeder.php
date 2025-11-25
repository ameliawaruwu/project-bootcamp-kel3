<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchants = [
            [
                'name' => 'Sate Solo Pak Komar',
                'description' => 'Sate dengan ciri khas budaya Solo',
                'address' => 'Jalan Sukapura No.76, Cipagalo, Kec. Bojongsoang, Kabupaten Bandung',
                'latitude' => -6.968525,
                'longitude' => 107.637171,
                'phone' => '0813-8288-5069',
                'is_active' => true,
                'opening_time' => '10:00',
                'closing_time' => '21:00',
            ],
            [
                'name' => 'Bakso Neng Amor 3',
                'description' => 'All you can eat bakso',
                'address' => 'Jl. Pungkur No.95j, Pungkur, Kec.Regol, Kota Bandung',
                'latitude' => -6.927291,
                'longitude' => 107.607725,
                'phone' => '021-23456789',
                'is_active' => true,
                'opening_time' => '09:00',
                'closing_time' => '17:00',
            ],
            [
                'name' => 'Geprek Maniak',
                'description' => 'Cita rasa geprek yang otentik',
                'address' => 'Jl.Cibadak No.295, Jamika, Kec.Bojongloa Kaler, Kota Bandung',
                'latitude' => -6.920069,
                'longitude' => 107.592653,
                'phone' => '021-34567890',
                'is_active' => true,
                'opening_time' => '10:00',
                'closing_time' => '18:00',
            ],
            [
                'name' => 'Nasi Goreng Aceng',
                'description' => 'Spesial nasi goreng bumbu rahasia Pak Aceng',
                'address' => 'Jl. Cibadak No.126, Cibadak, Kec. Astanaanyar, Kota Bandung',
                'latitude' => -6.920823,
                'longitude' => 107.596413,
                'phone' => '021-45678901',
                'is_active' => true,
                'opening_time' => '17:00',
                'closing_time' => '23:00',
            ],
            [
                'name' => 'Ketoprak Mas No',
                'description' => 'Ketoprak bumbu kacang',
                'address' => 'Jl. Mangga Dua No.25, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
                'latitude' => -6.1867,
                'longitude' => 106.8286,
                'phone' => '021-56789012',
                'is_active' => true,
                'opening_time' => '10:00',
                'closing_time' => '22:00',
            ],
        ];

        foreach ($merchants as $merchant) {
            Merchant::create($merchant);
        }
    }
}
