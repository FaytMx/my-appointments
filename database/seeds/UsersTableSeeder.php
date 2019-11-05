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
            'email' =>'silverzero55@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('mikimiki55'),
            'remember_token' => Str::random(10),
            'dni' => '12345678',
            'address' =>'',
            'phone' => '',
            'role' => 'admin'
        ]);
        factory(App\User::class, 50)->create();
    }
}
