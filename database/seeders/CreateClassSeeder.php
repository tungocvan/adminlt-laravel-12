<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\WpTerm;
use Modules\Category\Models\WpTermTaxonomy;

class CreateClassSeeder extends Seeder
{
    public function run()
    {
        if (WpTerm::count() == 0 && WpTermTaxonomy::count() == 0) {
            // Tạo WpTerm cho 'Khối lớp'
            $term = WpTerm::create([
                'name' => 'Khối lớp',
                'slug' => 'khoi-lop',
            ]);

            // Tạo WpTermTaxonomy cho 'Khối lớp'
            WpTermTaxonomy::create([
                'term_id' => $term->term_id,
                'taxonomy' => 'class_cat', // Giả sử taxonomy là 'category'
                'description' => 'Các khối lớp',
                'parent' => 0,
                'count' => 5, // Số lượng các khối lớp
            ]);

            // Tạo các WpTerm cho 'khối lớp 1' đến 'khối lớp 5'
            for ($i = 1; $i <= 5; $i++) {
                $subTerm = WpTerm::create([
                    'name' => "Khối lớp $i",
                    'slug' => "khoi-lop-$i",
                ]);

                // Tạo WpTermTaxonomy cho từng khối lớp
                WpTermTaxonomy::create([
                    'term_id' => $subTerm->term_id,
                    'taxonomy' => 'class_cat',
                    'description' => "Mô tả cho khối lớp $i",
                    'parent' => $term->term_id, // Tham chiếu đến khối lớp cha
                    'count' => 0, // Số lượng hiện tại, có thể cập nhật sau
                ]);
            }

            // Tạo WpTerm cho 'Môn học'
            $term = WpTerm::create([
                'name' => 'Môn học',
                'slug' => 'mon-hoc',
            ]);

            // Tạo WpTermTaxonomy cho 'Môn học'
            WpTermTaxonomy::create([
                'term_id' => $term->term_id,
                'taxonomy' => 'topic_cat', // Giả sử taxonomy là 'category'
                'description' => 'Danh sách các môn học',
                'parent' => 0,
                'count' => 3, // Số lượng các môn học
            ]);

            // Mảng chứa tên và slug của các môn học
            $subjects = [
                ['name' => 'Toán', 'slug' => 'toan'],
                ['name' => 'Tiếng Việt', 'slug' => 'tieng-viet'],
                ['name' => 'Tiếng Anh', 'slug' => 'tieng-anh'],
            ];

            // Tạo các WpTerm cho từng môn học
            foreach ($subjects as $subject) {
                $subTerm = WpTerm::create([
                    'name' => $subject['name'],
                    'slug' => $subject['slug'],
                ]);

                // Tạo WpTermTaxonomy cho từng môn học
                WpTermTaxonomy::create([
                    'term_id' => $subTerm->term_id,
                    'taxonomy' => 'topic_cat',
                    'description' => "Mô tả cho môn học: {$subject['name']}",
                    'parent' => $term->term_id, // Tham chiếu đến môn học cha
                    'count' => 0, // Số lượng hiện tại, có thể cập nhật sau
                ]);
            }

        }else {
            echo "Table already has data, skipping seeding.\n";
        }
    }
}
