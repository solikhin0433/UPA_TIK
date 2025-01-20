<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 1,
                'level_id' => 1,
                'username' => 'admin',
                'nama' => 'Administrator',
                'password' => Hash::make('12345678'), // Class untuk mengenkripsi/hash password
            ],
            [
                'user_id' => 2,
                'level_id' => 2,
                'username' => 'responden',
                'nama' => 'Responden',
                'password' => Hash::make('12345678'),
            ],
            [
                'user_id' => 3,
                'level_id' => 3,
                'username' => 'mpu',
                'nama' => 'Manajemen dan Pimpinan Unit',
                'password' => Hash::make('12345678'),
            ],
            [
                'user_id' => 4,
                'level_id' => 4,
                'username' => 'ver',
                'nama' => 'Verifikator',
                'password' => Hash::make('12345678'),
            ],
        ];
        
        DB::table('m_user')->insert($data);
    }
}