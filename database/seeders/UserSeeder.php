<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $users = [
        ['Admin CMMS',     'admin@cmms.test',  'Admin'],
        ['Manager Plant',  'manager@cmms.test', 'Manager'],
        ['Teknisi Budi',   'budi@cmms.test',   'Technician'],
        ['Teknisi Sari',   'sari@cmms.test',   'Technician'],
    ];

    foreach ($users as [$name, $email, $role]) {
        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password', // otomatis di-hash oleh cast 'hashed'
                'role_id' => Role::where('name', $role)->value('id'),
            ]
        );
    }
}
}
