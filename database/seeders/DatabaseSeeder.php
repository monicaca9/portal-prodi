<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // $id = guid();

        // \App\Models\User::insert([
        //     'id_pengguna' => $id,
        //     'username' => 'm.ikhsan',
        //     'password' => sha1('unilajaya'),
        //     'jenis_kelamin' => 'L',
        //     'approval_pengguna' => 1,
        //     'a_aktif' => 1,
        //     'tgl_create' => now(),
        //     'last_update' => now(),
        //     'soft_delete' => 0,
        //     'last_sync' => now(),
        //     'id_updater' => $id
        // ]);


        $id = 'db72c555-8291-4849-9fac-51f0aa0856a4'; // Ganti dengan ID pengguna yang sesuai
        $newPassword = 'unilajaya'; // Password baru

        \App\Models\User::where('id_pengguna', $id)
            ->update([
                'password' => sha1($newPassword), // Menggunakan Hash untuk menyimpan password dengan aman
                'last_update' => now(), // Update waktu terakhir
                'id_updater' => $id // ID updater, sesuaikan jika perlu
            ]);
    }
}