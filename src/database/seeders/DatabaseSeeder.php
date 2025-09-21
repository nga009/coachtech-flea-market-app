<?php

namespace Database\Seeders;

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
        // ユーザーテーブル
        $this->call(UsersSeeder::class);
        // 商品テーブル
        $this->call(ItemsSeeder::class);
        // カテゴリーテーブル
        $this->call(CategoriesSeeder::class);
    }
}
