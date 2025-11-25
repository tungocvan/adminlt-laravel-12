<?php 
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use Illuminate\Support\Facades\Storage;

class AreaJsonSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path("storage/app/areas.json");
       
        if (!file_exists($path)) {
            dd('File không tồn tại!');
        }
        $json = file_get_contents($path);
     
        $items = json_decode($json, true);       // convert thành array

        // Chunk để tránh insert quá nặng
        $chunks = array_chunk($items, 100);


        foreach ($chunks as $chunk) {
            $insertData = [];

            foreach ($chunk as $item) {
                $insertData[] = [
                    "code"           => $item["code"] ?? null,
                    "area_type"      => $item["areaType"] ?? null,
                    "name"           => $item["name"] ?? null,
                    "order_index"    => $item["orderIndex"] ?? null,
                    "status"         => $item["status"] ?? 0,
                    "created_date"   => $item["createdDate"] ?? null,
                    "created_by"     => $item["createdBy"] ?? null,
                    "parent_code"    => $item["parentCode"] ?? null,
                    "name_translate" => $item["nameTranslate"] ?? null,
                    "created_at"     => now(),
                    "updated_at"     => now(),
                ];
            }

            Area::insert($insertData); // insert 500 dòng/lần
        }
    }
}
