<?php
// use Illuminate\Support\Facades\File;

// if (!function_exists('json_file_ensure')) {
//     /**
//      * Đảm bảo file JSON tồn tại và có quyền đọc/ghi.
//      *
//      * @param string $path
//      * @param array|null $defaultData
//      * @return void
//      * @throws Exception
//      */
//     function json_file_ensure(string $path, array $defaultData = []): void
//     {
//         $dir = dirname($path);

//         // 🏗️ Tạo thư mục nếu chưa có
//         if (!File::exists($dir)) {
//             File::makeDirectory($dir, 0755, true);
//         }

//         // 🧾 Tạo file nếu chưa có
//         if (!File::exists($path)) {
//             File::put($path, json_encode($defaultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
//         }

//         // 🔒 Phân quyền đọc/ghi
//         if (!is_readable($path) || !is_writable($path)) {
//             @chmod($path, 0664); // rw-rw-r--
//         }

//         clearstatcache(true, $path);

//         if (!is_readable($path)) {
//             throw new \Exception("❌ Không thể đọc file JSON: {$path}");
//         }
//         if (!is_writable($path)) {
//             throw new \Exception("❌ Không thể ghi file JSON: {$path}");
//         }
//     }
// }

// if (!function_exists('json_file_read')) {
//     /**
//      * Đọc file JSON (đảm bảo tồn tại & hợp lệ).
//      *
//      * @param string $path
//      * @param array|null $defaultData
//      * @return array
//      */
//     function json_file_read(string $path, array $defaultData = []): array
//     {
//         json_file_ensure($path, $defaultData);

//         $content = File::get($path);
//         $data = json_decode($content, true);

//         if (json_last_error() !== JSON_ERROR_NONE) {
//             throw new \Exception("❌ Lỗi JSON: " . json_last_error_msg());
//         }

//         return $data ?? [];
//     }
// }

// if (!function_exists('json_file_write')) {
//     /**
//      * Ghi dữ liệu vào file JSON (tự động tạo nếu chưa có).
//      *
//      * @param string $path
//      * @param array $data
//      * @return bool
//      */
//     function json_file_write(string $path, array $data): bool
//     {
//         json_file_ensure($path);

//         $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//         if ($json === false) {
//             throw new \Exception("❌ Không thể encode JSON để ghi vào file: {$path}");
//         }

//         return File::put($path, $json) !== false;
//     }
// }
