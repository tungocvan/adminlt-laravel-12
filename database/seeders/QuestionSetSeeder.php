<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSetSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('question_sets')->count() == 0) {
            DB::table('question_sets')->insert([
                'user_id' => 1, // ID của người tạo bộ đề
                'category_ids' => 1, // ID Chuyên mục của bộ đề
                'question_type' => 'Một đáp án', // dạng trắc nghiệm bộ đề
                'questions' =>"[[[Chọn thủ đô của Việt Nam][Hà Nội|TP. Hồ Chí Minh|Đà Nẵng|Hải Phòng][0]],[[Chọn thủ đô của Nhật Bản][Tokyo|Kyoto|Osaka|Nara][0]],[[Ngọn núi cao nhất thế giới là?][Everest|Phú Sĩ|Alps|Rocky][0]],[[Chọn thủ đô của Việt Nam][Hà Nội|TP. Hồ Chí Minh|Đà Nẵng|Hải Phòng][0]],[[Chọn thủ đô của Nhật Bản][Tokyo|Kyoto|Osaka|Nara][0]],[[Ngọn núi cao nhất thế giới là?][Everest|Phú Sĩ|Alps|Rocky][0]],[[Chọn thủ đô của Việt Nam][Hà Nội|TP. Hồ Chí Minh|Đà Nẵng|Hải Phòng][0]],[[Chọn thủ đô của Nhật Bản][Tokyo|Kyoto|Osaka|Nara][0]],[[Ngọn núi cao nhất thế giới là?][Everest|Phú Sĩ|Alps|Rocky][0]]]",
                'total_questions' => 9, // tổng số có 9 câu hỏi
                'timeRemaining' => 10, // thời gian bộ đề 10 phút
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }else {
            echo "Table already has data, skipping seeding.\n";
        }
    }
}
