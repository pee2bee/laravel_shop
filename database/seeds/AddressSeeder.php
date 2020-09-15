<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //取出用户id
        $users = User::all()->pluck('id')->toArray();
        //调用工厂，生成数据
        $addresses = factory(Address::class)->times(100)->make()->each(
            function ($item, $index) use ($users) {
                $item->user_id = $users[array_rand($users)];
                return $item;
            }
        )->toArray();

        //执行插入
       Address::insert($addresses);
    }
}
