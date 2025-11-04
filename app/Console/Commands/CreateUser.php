<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TnvUserHelper;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CreateUser extends Command
{
    /**
     * Cú pháp:
     * php artisan create:user [--email=] [--password=] [--name=] [--role=]
     *                         [--verified] [--admin]
     *                         [--import=] [--template]
     */
    protected $signature = 'create:user
    {--email= : Địa chỉ email của người dùng}
    {--password= : Mật khẩu của người dùng}
    {--name= : Tên hiển thị (tùy chọn)}
    {--role= : Vai trò (tùy chọn, mặc định User)}
    {--verified : Đánh dấu email đã xác minh}
    {--admin : Tạo user với quyền Admin (tự động gán role Admin)}
    {--import= : Đường dẫn file Excel cần import}
    {--template= : Số lượng user mẫu để tạo file template Excel}';


    protected $description = 'Tạo nhanh user mới hoặc import danh sách user từ file Excel.';
    public function handle()
    {
        // --- Xử lý tạo file template ---
        $templateOption = $this->option('template');
        if ($templateOption !== null) {
            $count = (int) $templateOption;
    
            if ($count <= 0) {
                $this->warn('⚠️ Giá trị của --template phải là số nguyên dương (ví dụ: --template=10)');
                return Command::FAILURE;
            }
    
            return $this->createTemplateFile($count);
        }
    
        // --- Xử lý import ---
        $importFile = $this->option('import');
        if (!empty($importFile)) {
            return $this->importUsers($importFile);
        }
    
        // --- Xử lý tạo 1 user đơn ---
        return $this->createSingleUser();
    }
    

    /**
     * Tạo 1 user thủ công
     */
    protected function createSingleUser()
    {
        $email     = $this->option('email');
        $password  = $this->option('password');
        $name      = $this->option('name');
        $role      = $this->option('role') ?? 'User';
        $verified  = $this->option('verified') ?? false;
        $isAdmin   = $this->option('admin') ? 1 : 0;

        if ($isAdmin) {
            $role = 'Admin';
            $verified = true;
        }

        if (empty($email)) {
            $email = $this->ask('Nhập email');
        }

        if (empty($password)) {
            $password = $this->secret('Nhập mật khẩu (sẽ ẩn)');
        }

        $data = [
            'email'     => $email,
            'password'  => $password,
            'name'      => $name,
            'role_name' => $role,
            'verified'  => $verified,
            'is_admin'  => $isAdmin,
        ];

        $this->info('⏳ Đang tạo user...');

        $result = TnvUserHelper::register($data);

        if ($result['status'] === 'success') {
            $this->info('✅ Tạo user thành công!');
            $this->table(
                ['ID', 'Name', 'Email', 'Username', 'is_admin', 'Roles'],
                [[
                    $result['data']['id'],
                    $result['data']['name'],
                    $result['data']['email'],
                    $result['data']['username'],
                    $result['data']['is_admin'],
                    implode(', ', $result['data']['roles']->toArray()),
                ]]
            );

            if (!empty($result['token'])) {
                $this->line('🔑 API Token: ' . $result['token']);
            }
        } else {
            $this->error('❌ ' . $result['message']);
        }

        return Command::SUCCESS;
    }

    /**
     * Import danh sách user từ Excel
     */
    protected function importUsers($importFile)
    {
        if (!str_contains($importFile, '/')) {
            $importFile = storage_path('app/public/excel/database/' . $importFile);
        }
    
        if (!File::exists($importFile)) {
            $this->error("❌ File không tồn tại: {$importFile}");
            return Command::FAILURE;
        }
    
        $this->info("📂 Đang đọc file: {$importFile}");
    
        try {
            $spreadsheet = IOFactory::load($importFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
    
            if (count($rows) < 2) {
                $this->warn("⚠️ File không có dữ liệu để import.");
                return Command::SUCCESS;
            }
    
            $header = array_map(fn($h) => strtolower(trim($h)), $rows[1]);
            unset($rows[1]);
    
            $created = 0;
            $skipped = 0;
            $failed  = 0;
    
            foreach ($rows as $index => $row) {
                $data = array_combine($header, array_values($row));
    
                if (empty($data['email']) || empty($data['password'])) {
                    $this->warn("⚠️ Dòng {$index}: Thiếu email hoặc password → bỏ qua.");
                    $skipped++;
                    continue;
                }
    
                $userData = [
                    'email'      => trim($data['email']),
                    'password'   => trim($data['password']),
                    'name'       => $data['name'] ?? null,
                    'username'   => $data['username'] ?? null,
                    'role_name'  => $data['role'] ?? 'User',
                    'is_admin'   => isset($data['is_admin']) ? (int)$data['is_admin'] : 0,
                    'verified'   => !empty($data['verified']),
                ];
    
                $result = TnvUserHelper::register($userData);
    
                if ($result['status'] === 'success') {
                    $created++;
                    $roles = is_array($result['data']['roles'])
                        ? implode(', ', $result['data']['roles'])
                        : $result['data']['roles']->implode(', ');
    
                    $this->line("✅ {$created}. {$result['data']['email']} ({$roles})");
                } else {
                    $failed++;
                    $this->warn("❌ Dòng {$index} - {$data['email']} → {$result['message']}");
                }
            }
    
            $this->newLine();
            $this->info("🎯 Import hoàn tất!");
            $this->line("📊 Kết quả:");
            $this->line("   ✅ Tạo mới:  {$created}");
            $this->line("   ⚠️ Bỏ qua:    {$skipped}");
            $this->line("   ❌ Lỗi:       {$failed}");
    
            return Command::SUCCESS;
    
        } catch (\Throwable $e) {
            $this->error("❌ Import thất bại: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    

    /**
     * Tạo file Excel mẫu để import user
     */
    protected function createTemplateFile($count = 5)
    {
        $path = storage_path('app/public/excel/database/');
        $filename = 'user_template.xlsx';
        $file = $path . $filename;

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cột tiêu đề
        $headers = ['email', 'password', 'name', 'username', 'role', 'is_admin', 'verified'];
        $sheet->fromArray($headers, null, 'A1');

        // Danh sách role mẫu
        $roles = ['User', 'Admin', 'Editor', 'Agent'];

        // Tạo ngẫu nhiên $count dòng
        $rows = [];
        for ($i = 1; $i <= $count; $i++) {
            $name = fake()->name();
            $username = strtolower(str_replace(' ', '', fake()->firstName())) . rand(10, 99);
            $email = $username . '@example.com';
            $password = fake()->password(6, 10);
            $role = $roles[array_rand($roles)];
            $isAdmin = $role === 'Admin' ? 1 : 0;
            $verified = rand(0, 1);

            $rows[] = [$email, $password, $name, $username, $role, $isAdmin, $verified];
        }

        // Ghi dữ liệu vào Excel
        $sheet->fromArray($rows, null, 'A2');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);

        $this->info("📘 File mẫu đã được tạo: {$file}");
        $this->info("📄 Tổng cộng {$count} dòng dữ liệu ngẫu nhiên được sinh ra.");
        $this->line("👉 Bạn có thể chỉnh sửa và import lại bằng:");
        $this->line("   php artisan create:user --import=user_template.xlsx");

        return Command::SUCCESS;
    }

}
