<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         $this->call(UsersTableSeeder::class);
//        factory(App\User::class, 2)->create();

//        DB::table('users')->insert([
//            'name' => 'admin',
//            'email' => 'admin@gmail.com',
//            'password' => bcrypt('admin'),
//        ]);
        DB::table('currencies')->insert([
            [
                'name' => 'Евро',
                'code' => 'R01239'
            ],
            [
                'name' => 'Доллар США',
                'code' => 'R01235'
            ]
        ]);
    }
}
