<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (DB::table('questions')->count() == 0) {
             // Mảng dữ liệu mẫu cho các câu hỏi
                $questions = [
                    [
                        'user_id' => 1,
                        'category_class_id' => 0,
                        'category_topic_id' => 1,
                        'question_details' => '[Chọn thủ đô của Việt Nam][Hà Nội|TP. Hồ Chí Minh|Đà Nẵng|Hải Phòng][0]',
                        'question_type' => 'Một đáp án',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => 1,
                        'category_class_id' => 0,
                        'category_topic_id' => 1,
                        'question_details' => '[Chọn thủ đô của Nhật Bản][Tokyo|Kyoto|Osaka|Nara][0]',
                        'question_type' => 'Một đáp án',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => 1,
                        'category_class_id' => 0,
                        'category_topic_id' => 1,
                        'question_details' => '[Ngọn núi cao nhất thế giới là?][Everest|Phú Sĩ|Alps|Rocky][0]',
                        'question_type' => 'Một đáp án',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    // Thêm các câu hỏi khác tương tự ở đây...
                ];
            // Thêm 10 câu hỏi vào bảng `questions`
            for ($i = 0; $i < 10; $i++) {
                DB::table('questions')->insert($questions[$i % 3]); // Lặp lại 3 câu hỏi mẫu, bạn có thể thêm nhiều câu hỏi khác vào mảng
            }
        }else {
            echo "Table already has data, skipping seeding.\n";
        }
    }
}
