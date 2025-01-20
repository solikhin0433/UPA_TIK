<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Insert Menu Utama
        $polinema = DB::table('menus')->insertGetId([
            'name' => 'POLINEMA',
            'parent_id' => null, // Ini menu utama tanpa parent
            'order_number' => 1,
            'slug' => 'polinema',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Sub-menu untuk POLINEMA
        DB::table('menus')->insert([
            ['name' => 'SIAKAD', 'parent_id' => $polinema, 'order_number' => 1, 'slug' => 'siakad', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SPMB', 'parent_id' => $polinema, 'order_number' => 2, 'slug' => 'spmb', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ALUMNI', 'parent_id' => $polinema, 'order_number' => 3, 'slug' => 'alumni', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert Menu Utama untuk header lainnya
        $beranda = DB::table('menus')->insertGetId([
            'name' => 'Beranda',
            'parent_id' => null, // Ini menu utama tanpa parent
            'order_number' => 2,
            'slug' => 'beranda',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Sub-menu untuk Beranda
        DB::table('menus')->insert([
            ['name' => 'Tentang Kami', 'parent_id' => $beranda, 'order_number' => 1, 'slug' => 'tentang-kami', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'E-Form', 'parent_id' => $beranda, 'order_number' => 2, 'slug' => 'e-form', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Informasi Publik', 'parent_id' => $beranda, 'order_number' => 3, 'slug' => 'informasi-publik', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}