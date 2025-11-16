<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'user_id' => 1,
                'condition' =>'良好' ,
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image' => 'images/Armani+Mens+Clock.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 2,
                'condition' => '目立った傷や汚れなし',
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'item_image' => 'images/HDD+Hard+Disk.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 3,
                'condition' => 'やや傷や汚れあり',
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image' => 'images/iLoveIMG+d.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 1,
                'condition' => '状態が悪い',
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'item_image' => 'images/Leather+Shoes+Product+Photo.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 2,
                'condition' =>'良好' ,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'item_image' => 'images/Living+Room+Laptop.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 1,
                'condition' => '目立った傷や汚れなし',
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'item_image' => 'images/Music+Mic+4632231.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 2,
                'condition' => 'やや傷や汚れあり',
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'item_image' => 'images/Purse+fashion+pocket.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 3,
                'condition' => '状態が悪い',
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => 'なし',
                'description' => '使いやすいタンブラー',
                'item_image' => 'images/Tumbler+souvenir.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 2,
                'condition' =>'良好' ,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'item_image' => 'images/Waitress+with+Coffee+Grinder.jpg',
                'is_sold' => false,
            ],

            [
                'user_id' => 1,
                'condition' => '目立った傷や汚れなし',
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => null,
                'description' => '便利なメイクセット',
                'item_image' => 'images/外出メイクアップセット.jpg',
                'is_sold' => false,
            ],
        ];
        foreach ($items as $data) {
            Item::firstOrCreate(['name' => $data['name']], $data);
        }

        $items = Item::all();
        foreach ($items as $item) {
            $filename = basename($item->item_image);

            $source = "dummy_images/{$filename}";   // コピー元: storage/app/public/dummy_images
            $destination = "images/{$filename}";    // コピー先: storage/app/public/images

            if (Storage::disk('public')->exists($source) && !Storage::disk('public')->exists($destination)) {
                Storage::disk('public')->copy($source, $destination);
            }

            $item->update(['item_image' => $destination]);
        }
    }
}
