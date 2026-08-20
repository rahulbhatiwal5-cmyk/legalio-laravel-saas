<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminLoginRegisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('login_registers')->where('id', 1)->update([
            'meta_title' => 'Iniciar sesión',
        ]);
        DB::table('login_registers')->where('id', 2)->update([
            'meta_title' => 'Crear cuenta',
        ]);
        DB::table('login_registers')->where('id', 3)->update([
            'meta_title' => 'Crear documento',
        ]);
    }
}
