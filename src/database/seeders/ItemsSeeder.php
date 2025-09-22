<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // サンプル画像のコピー
        $srcDir = base_path('database/seeders/assets/items'); 
        $dstDir = 'item_images';

        foreach (glob($srcDir.'/*.{jpg,png,gif,webp}', GLOB_BRACE) as $path) {
            $filename = basename($path);

            // public ディスク(storage/app/public/)にコピー
            Storage::disk('public')->put("$dstDir/$filename", file_get_contents($path));
        }

        $items = [
            [
                'item_image' => 'item_images/Armani+Mens+Clock.jpg',
                'condition' => 1,
                'name' => '腕時計',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'seller_id' => '1',
            ],
            [
                'item_image' => 'item_images/HDD+Hard+Disk.jpg',
                'condition' => 2,
                'name' => 'HDD',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'seller_id' => '1',
            ],
            [
                'item_image' => 'item_images/iLoveIMG+d.jpg',
                'condition' => 3,
                'name' => '玉ねぎ3束',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'seller_id' => '1',
            ],
            [
                'item_image' => 'item_images/Leather+Shoes+Product+Photo.jpg',
                'condition' => 4,
                'name' => '革靴',
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'seller_id' => '2',
            ],
            [
                'item_image' => 'item_images/Living+Room+Laptop.jpg',
                'condition' => 1,
                'name' => 'ノートPC',
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'seller_id' => '2',
            ],
            [
                'item_image' => 'item_images/Music+Mic+4632231.jpg',
                'condition' => 2,
                'name' => 'マイク',
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'seller_id' => '2',
            ],
            [
                'item_image' => 'item_images/Purse+fashion+pocket.jpg',
                'condition' => 3,
                'name' => 'ショルダーバッグ',
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'seller_id' => '1',
            ],
            [
                'item_image' => 'item_images/Tumbler+souvenir.jpg',
                'condition' => 4,
                'name' => 'タンブラー',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'seller_id' => '1',
            ],
            [
                'item_image' => 'item_images/Waitress+with+Coffee+Grinder.jpg',
                'condition' => 1,
                'name' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'seller_id' => '1',
            ],
            [
                'item_image' => 'item_images/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => 2,
                'name' => 'メイクセット',
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'seller_id' => '1',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

    }
}
