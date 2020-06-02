<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建50个假数据

        $users = factory(User::class)->times(50)->make();
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());

        //更新第一个数据，并添加为管理员
        $user = User::find(1);
        $user->name = 'Summer';
        $user->email = 'summer@example.com';
        $user->is_admin = true;
        $user->save();
    }

}
