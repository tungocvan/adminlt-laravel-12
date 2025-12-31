<?php

namespace Database\Seeders;

use App\Models\WpProduct;
use Illuminate\Database\Seeder;

class WpProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'title' => 'Trà Xanh Matcha Nhật Bản',
                'slug' => 'tra-xanh-matcha-nhat-ban',
                'short_description' => 'Trà matcha nguyên chất từ Nhật Bản, giàu chất chống oxi hóa',
                'description' => 'Trà matcha cao cấp nhập khẩu trực tiếp từ vùng Uji, Kyoto. Được trồng và thu hoạch theo phương pháp truyền thống, mang đến hương vị đậm đà và giá trị dinh dưỡng tối đa.',
                'regular_price' => 350000,
                'sale_price' => 299000,
                'image' => 'https://via.placeholder.com/600x600/4CAF50/FFFFFF?text=Matcha',
                'gallery' => ['https://via.placeholder.com/600/4CAF50', 'https://via.placeholder.com/600/8BC34A'],
                'tags' => ['Trà Xanh', 'Nhật Bản', 'Organic'],
            ],
            [
                'title' => 'Cà Phê Arabica Đà Lạt',
                'slug' => 'ca-phe-arabica-da-lat',
                'short_description' => 'Hạt cà phê Arabica nguyên chất từ cao nguyên Đà Lạt',
                'description' => 'Cà phê Arabica được trồng tại độ cao 1500m, khí hậu mát mẻ quanh năm. Hạt cà phê được rang mới mỗi tuần, đảm bảo độ tươi và hương thơm tự nhiên.',
                'regular_price' => 180000,
                'sale_price' => null,
                'image' => 'https://via.placeholder.com/600x600/795548/FFFFFF?text=Coffee',
                'gallery' => null,
                'tags' => ['Cà Phê', 'Việt Nam', 'Arabica'],
            ],
            [
                'title' => 'Mật Ong Rừng Nguyên Chất',
                'slug' => 'mat-ong-rung-nguyen-chat',
                'short_description' => 'Mật ong rừng tự nhiên 100%, không pha trộn',
                'description' => 'Mật ong được khai thác từ các tổ ong rừng tự nhiên tại Tây Nguyên. Giàu enzyme, vitamin và khoáng chất tự nhiên, tốt cho sức khỏe.',
                'regular_price' => 450000,
                'sale_price' => 399000,
                'image' => 'https://via.placeholder.com/600x600/FFC107/FFFFFF?text=Honey',
                'gallery' => ['https://via.placeholder.com/600/FFC107', 'https://via.placeholder.com/600/FFEB3B'],
                'tags' => ['Mật Ong', 'Organic', 'Tây Nguyên'],
            ],
        ];

        foreach ($products as $product) {
            WpProduct::create($product);
        }
    }
}
