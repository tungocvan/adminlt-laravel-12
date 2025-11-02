<?php

namespace App\Livewire\Env;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

class EnvList extends Component
{
    public $email = [];

    public $app = [];
    public $cache = [];
    public $gmail = [];
    public $database = [
        'connection' => '',
        'host' => '',
        'port' => '',
        'database' => '',
        'username' => '',
        'password' => '',
    ];

    public $gmailTestResult = null;
    public $gmailTestSuccess = false;

    public $testResult = null;
    public $testSuccess = false;


    public function mount()
    {
        $this->loadEnvData();
    }

    /**
     * Load current .env values into component state
     */
    protected function loadEnvData()
    {
        $this->email = [
            'mailer'       => env('MAIL_MAILER'),
            'host'         => env('MAIL_HOST'),
            'port'         => env('MAIL_PORT'),
            'username'     => env('MAIL_USERNAME'),
            'password'     => env('MAIL_PASSWORD'),
            'encryption'   => env('MAIL_ENCRYPTION'),
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name'    => env('MAIL_FROM_NAME'),
        ];

        $this->database = [
            'connection' => env('DB_CONNECTION'),
            'host'       => env('DB_HOST'),
            'port'       => env('DB_PORT'),
            'database'   => env('DB_DATABASE'),
            'username'   => env('DB_USERNAME'),
            'password'   => env('DB_PASSWORD'),
        ];

        $this->app = [
            'name'  => env('APP_NAME'),
            'env'   => env('APP_ENV'),
            'debug' => env('APP_DEBUG', false) ? 'true' : 'false',
            'url'   => env('APP_URL'),
        ];

        $this->cache = [
            'cache_driver'    => env('CACHE_DRIVER'),
            'queue_connection' => env('QUEUE_CONNECTION'),
            'session_driver'  => env('SESSION_DRIVER'),
        ];
        $this->gmail = [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect' => env('GOOGLE_REDIRECT'),
        ];
    }

    /**
     * Backup .env before saving
     */
    public function backupEnv()
    {
        $envPath = base_path('.env');
        $backupPath = base_path('.env.backup');

        if (!File::exists($envPath)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => '.env không tồn tại!']);
            return;
        }

        File::copy($envPath, $backupPath);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Đã sao lưu .env → .env.backup thành công!']);
    }

    /**
     * Update Email configuration
     */
    public function updateEmailConfig()
    {
        $this->backupEnv();
        $this->updateEnv([
            'MAIL_MAILER'       => $this->email['mailer'],
            'MAIL_HOST'         => $this->email['host'],
            'MAIL_PORT'         => $this->email['port'],
            'MAIL_USERNAME'     => $this->email['username'],
            'MAIL_PASSWORD'     => $this->email['password'],
            'MAIL_ENCRYPTION'   => $this->email['encryption'],
            'MAIL_FROM_ADDRESS' => $this->email['from_address'],
            'MAIL_FROM_NAME'    => $this->email['from_name'],
        ]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Cập nhật cấu hình Email thành công!']);
    }

    public function updateDatabaseConfig()
    {
        $this->backupEnv();
        $this->updateEnv([
            'DB_CONNECTION' => $this->database['connection'],
            'DB_HOST'       => $this->database['host'],
            'DB_PORT'       => $this->database['port'],
            'DB_DATABASE'   => $this->database['database'],
            'DB_USERNAME'   => $this->database['username'],
            'DB_PASSWORD'   => $this->database['password'],
        ]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Cập nhật cấu hình Database thành công!']);
    }

    public function updateAppConfig()
    {
        $this->backupEnv();
        $this->updateEnv([
            'APP_NAME'  => $this->app['name'],
            'APP_ENV'   => $this->app['env'],
            'APP_DEBUG' => $this->app['debug'],
            'APP_URL'   => $this->app['url'],
        ]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Cập nhật cấu hình Ứng dụng thành công!']);
    }

    public function updateCacheConfig()
    {
        $this->backupEnv();
        $this->updateEnv([
            'CACHE_DRIVER'     => $this->cache['cache_driver'],
            'QUEUE_CONNECTION' => $this->cache['queue_connection'],
            'SESSION_DRIVER'   => $this->cache['session_driver'],
        ]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Cập nhật Cache & Queue thành công!']);
    }

    /**
     * Update environment variables safely
     */
    protected function updateEnv(array $data)
    {
        $envPath = base_path('.env');
        $env = File::get($envPath);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $escapedValue = $this->escapeEnvValue($value);

            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, "{$key}={$escapedValue}", $env);
            } else {
                $env .= "\n{$key}={$escapedValue}";
            }
        }

        File::put($envPath, trim($env) . "\n");

        // Reload configuration
        Artisan::call('config:clear');
        Artisan::call('config:cache');
    }

    protected function escapeEnvValue($value)
    {
        if ($value === null) return '';
        if (str_contains($value, ' ') || str_contains($value, '#')) {
            return '"' . trim($value) . '"';
        }
        return trim($value);
    }

    public function testConnection()
    {
        try {
            // Tạo cấu hình tạm thời
            $config = [
                'driver'   => $this->database['connection'] ?? 'mysql',
                'host'     => $this->database['host'] ?? '127.0.0.1',
                'port'     => $this->database['port'] ?? '3306',
                'database' => $this->database['database'] ?? '',
                'username' => $this->database['username'] ?? '',
                'password' => $this->database['password'] ?? '',
            ];

            // Tạo kết nối tạm thời không ảnh hưởng config chính
            config(['database.connections.temp_test' => $config]);
            DB::purge('temp_test');
            DB::connection('temp_test')->getPdo();

            $this->testSuccess = true;
            $this->testResult = '✅ Kết nối cơ sở dữ liệu thành công!';
        } catch (Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Kết nối thất bại: ' . $e->getMessage()
            ]);
        }
    }

    public function testGmailConnection()
    {
        try {
            // Bạn có thể gọi route Google OAuth để test redirect hợp lệ
            // Hoặc kiểm tra đủ thông tin trước
            if (!$this->gmail['client_id'] || !$this->gmail['client_secret'] || !$this->gmail['redirect']) {
                throw new \Exception('Thiếu thông tin cấu hình Gmail.');
            }

            $this->gmailTestSuccess = true;
            $this->gmailTestResult = '✅ Cấu hình Gmail hợp lệ.';
        } catch (\Exception $e) {
            $this->gmailTestSuccess = false;
            $this->gmailTestResult = '❌ ' . $e->getMessage();
        }
    }

    public function updateGmailConfig()
    {
        // Sao lưu .env
        $envPath = base_path('.env');
        copy($envPath, base_path('.env.backup'));

        // Ghi lại biến vào .env
        $env = file_get_contents($envPath);
        $env = preg_replace('/GOOGLE_CLIENT_ID=.*/', 'GOOGLE_CLIENT_ID=' . $this->gmail['client_id'], $env);
        $env = preg_replace('/GOOGLE_CLIENT_SECRET=.*/', 'GOOGLE_CLIENT_SECRET=' . $this->gmail['client_secret'], $env);
        $env = preg_replace('/GOOGLE_REDIRECT_URI=.*/', 'GOOGLE_REDIRECT_URI=' . $this->gmail['redirect'], $env);
        file_put_contents($envPath, $env);

        session()->flash('message', 'Đã cập nhật cấu hình Gmail và sao lưu .env.backup');
    }


    public function testEmailConnection()
    {
        try {
            // Áp dụng cấu hình tạm thời
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $this->email['host'],
                'port' => $this->email['port'],
                'encryption' => $this->email['encryption'],
                'username' => $this->email['username'],
                'password' => $this->email['password'],
            ]);

            Config::set('mail.from', [
                'address' => $this->email['from_address'],
                'name' => $this->email['from_name'],
            ]);

            // Gửi email test
            Mail::raw('Đây là email kiểm tra kết nối mail từ hệ thống.', function ($message) {
                $message->to($this->email['from_address'])
                    ->subject('✅ Kiểm tra kết nối Email thành công');
            });

            $this->dispatch('alert-success', message: 'Gửi mail kiểm tra thành công! Vui lòng kiểm tra hộp thư.');
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email test: ' . $e->getMessage());
            $this->dispatch('alert-error', message: 'Không thể gửi mail: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.env.env-list');
    }
}
