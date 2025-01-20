<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    public function run()
    {
        // Insert Pages untuk POLINEMA
        DB::table('pages')->insert([
            [
                'title' => 'SIAKAD Page',
                'thumbnail' => 'siakad-thumbnail.jpg',
                'user_id' => 1, // ID user creator
                'content' => '<p>Ini adalah konten untuk halaman SIAKAD.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'SPMB Page',
                'thumbnail' => 'spmb-thumbnail.jpg',
                'user_id' => 1, // ID user creator
                'content' => '<p>Ini adalah konten untuk halaman SPMB.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'ALUMNI Page',
                'thumbnail' => 'alumni-thumbnail.jpg',
                'user_id' => 1, // ID user creator
                'content' => '<p>Ini adalah konten untuk halaman ALUMNI.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert Pages untuk Beranda
        DB::table('pages')->insert([
            [
                'title' => 'Tentang Kami Page',
                'thumbnail' => 'tentang-kami-thumbnail.jpg',
                'user_id' => 1, // ID user creator
                'content' => '<p>Ini adalah konten untuk halaman Tentang Kami.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'E-Form Page',
                'thumbnail' => 'e-form-thumbnail.jpg',
                'user_id' => 1, // ID user creator
                'content' => '<p>Ini adalah konten untuk halaman E-Form.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Informasi Publik Page',
                'thumbnail' => 'informasi-publik-thumbnail.jpg',
                'user_id' => 1, // ID user creator
                'content' => '<p>Ini adalah konten untuk halaman Informasi Publik.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}