<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
            'name' => 'Emanuel vargas',
            'email' => 'silverzero55@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('mikimiki55'),
            'remember_token' => Str::random(10),
            'dni' => '12345678',
            'address' => '',
            'phone' => '',
            'role' => 'admin'
        ]);

        App\User::create([
            'name' => 'Doctor Test',
            'email' => 'doctor@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('mikimiki55'),
            'remember_token' => Str::random(10),
            'dni' => '12345678',
            'address' => '',
            'phone' => '',
            'role' => 'doctor'
        ]);

        App\User::create([
            'name' => 'Paciente Test',
            'email' => 'patient@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('mikimiki55'),
            'remember_token' => Str::random(10),
            'dni' => '12345678',
            'address' => '',
            'phone' => '',
            'role' => 'patient'
        ]);

        factory(App\User::class, 50)->states('patient')->create();
    }
}
