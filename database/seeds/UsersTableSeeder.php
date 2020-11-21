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
        $n=10;
        for ($i=1;$i<=$n;$i++) {
            $user = 'user'.$i;
            DB::table('users')->insert([
                'name' => $user,
                'email' => $user . '@gmail.com',
                'password' => bcrypt('pswd'.$user),
            ]);
        }
    }
}
