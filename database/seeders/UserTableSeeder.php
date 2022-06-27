<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'firstName' => 'Tahir',
            'LastName' => 'Aghazada',
            'role' => 1,
            'status' => 1,
            'email' => 'agazadetahir@gmail.com',
            'password' => Hash::make('12345678')
        ]);
    }
}
