<?php

use Illuminate\Database\Seeder;

class EndUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\EndUser::class, 500)->create();
    }
}
