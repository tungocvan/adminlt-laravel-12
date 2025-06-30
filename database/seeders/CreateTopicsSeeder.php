<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Category\Models\WpTerm;
use Modules\Category\Models\WpTermTaxonomy;
use App\Models\Option;

class CreateTopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Option::count() == 0) {
            $topic = ['Toan','Tiếng Việt','Tiếng Anh'];
            $class = ['Khối lớp 1','Khối lớp 2','Khối lớp 3','Khối lớp 4','Khối lớp 5'];
            $class = ['Khối lớp 1','Khối lớp 2','Khối lớp 3','Khối lớp 4','Khối lớp 5'];
            $capdo = ['Biết', 'Hiểu', 'Vận dụng'];
            $loaicau = ['Một đáp án','Đúng/Sai','Nhiều đáp án', 'Tự luận'];
            $dapan = ['Đáp án 1','Đáp án 2','Đáp án 3','Đáp án 4'];
            Option::set_option('quiz_monhoc',$topic);
            Option::set_option('quiz_khoilop',$class);
            Option::set_option('quiz_capdo', $capdo);
            Option::set_option('quiz_loaicau', $loaicau);
            Option::set_option('quiz_dapan',$dapan);
        }else {
            echo "Table already has data, skipping seeding.\n";
        }
    }
}
