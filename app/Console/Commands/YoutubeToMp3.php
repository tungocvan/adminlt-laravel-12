<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class YoutubeToMp3 extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'youtube:mp3 
        {url : Link YouTube cần tách mp3}
        {--output=storage/app/youtube : Thư mục lưu file}
        {--cookies= : Browser để lấy cookies (firefox, chrome, chromium, edge, opera, brave)}
        {--cookies-file= : Đường dẫn file cookies.txt}';

    /**
     * The console command description.
     */
    protected $description = 'Tải video YouTube và chuyển đổi sang MP3 sử dụng yt-dlp';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $url = $this->argument('url');
        $outputDir = $this->option('output');
        $cookiesBrowser = $this->option('cookies');
        $cookiesFile = $this->option('cookies-file');

        // Validate URL
        if (!$this->isValidYoutubeUrl($url)) {
            $this->error('❌ URL YouTube không hợp lệ!');
            return Command::FAILURE;
        }

        // Create output directory if not exists
        $fullOutputPath = base_path($outputDir);
        if (!is_dir($fullOutputPath)) {
            mkdir($fullOutputPath, 0755, true);
            $this->info("📁 Đã tạo thư mục: {$outputDir}");
        }

        $this->newLine();
        $this->info('🎵 YouTube to MP3 Converter');
        $this->info('═══════════════════════════════════════');
        $this->info("📺 URL: {$url}");
        $this->info("📂 Output: {$outputDir}");
        if ($cookiesBrowser) {
            $this->info("🍪 Cookies: {$cookiesBrowser}");
        }
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        // Get video info first
        $this->info('📡 Đang lấy thông tin video...');
        $videoInfo = $this->getVideoInfo($url, $cookiesBrowser, $cookiesFile);
        
        if ($videoInfo) {
            $this->info("📌 Tiêu đề: {$videoInfo['title']}");
            $this->info("⏱️  Thời lượng: {$videoInfo['duration']}");
            $this->info("👤 Channel: {$videoInfo['channel']}");
            $this->newLine();
        }

        // Download and convert
        $this->info('⬇️  Đang tải và chuyển đổi sang MP3...');
        $this->newLine();

        $result = $this->downloadAndConvert($url, $fullOutputPath, $cookiesBrowser, $cookiesFile);

        if ($result['success']) {
            $this->newLine();
            $this->info('═══════════════════════════════════════');
            $this->info('✅ Hoàn thành!');
            $this->info("📁 File: {$result['filename']}");
            $this->info('═══════════════════════════════════════');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->error('❌ Lỗi: ' . $result['error']);
        
        // Show helpful tips
        if (str_contains($result['error'], 'Sign in') || str_contains($result['error'], 'bot')) {
            $this->newLine();
            $this->warn('💡 Gợi ý khắc phục:');
            $this->line('   1. Cập nhật yt-dlp: sudo yt-dlp -U');
            $this->line('   2. Sử dụng cookies: php artisan youtube:mp3 "URL" --cookies=firefox');
            $this->line('   3. Hoặc dùng cookies file: --cookies-file=/path/to/cookies.txt');
        }
        
        return Command::FAILURE;
    }

    /**
     * Validate YouTube URL
     */
    private function isValidYoutubeUrl(string $url): bool
    {
        $patterns = [
            '/^https?:\/\/(www\.)?youtube\.com\/watch\?v=[\w-]+/',
            '/^https?:\/\/youtu\.be\/[\w-]+/',
            '/^https?:\/\/(www\.)?youtube\.com\/shorts\/[\w-]+/',
            '/^https?:\/\/(www\.)?youtube\.com\/live\/[\w-]+/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build common yt-dlp arguments
     */
    private function buildCookiesArgs(?string $browser, ?string $cookiesFile): array
    {
        $args = [];

        if ($cookiesFile && file_exists($cookiesFile)) {
            $args[] = '--cookies';
            $args[] = $cookiesFile;
        } elseif ($browser) {
            $validBrowsers = ['firefox', 'chrome', 'chromium', 'edge', 'opera', 'brave', 'vivaldi', 'safari'];
            if (in_array(strtolower($browser), $validBrowsers)) {
                $args[] = '--cookies-from-browser';
                $args[] = strtolower($browser);
            }
        }

        return $args;
    }

    /**
     * Get video information
     */
    private function getVideoInfo(string $url, ?string $browser, ?string $cookiesFile): ?array
    {
        $args = array_merge(
            ['yt-dlp', '--dump-json', '--no-download'],
            $this->buildCookiesArgs($browser, $cookiesFile),
            [
                '--no-warnings',
                '--extractor-args', 'youtube:player_client=android',
                $url
            ]
        );

        $process = new Process($args);
        $process->setTimeout(120);

        try {
            $process->run();

            if ($process->isSuccessful()) {
                $json = json_decode($process->getOutput(), true);
                
                $duration = $json['duration'] ?? 0;
                $minutes = floor($duration / 60);
                $seconds = $duration % 60;

                return [
                    'title' => $json['title'] ?? 'Unknown',
                    'duration' => sprintf('%d:%02d', $minutes, $seconds),
                    'channel' => $json['channel'] ?? $json['uploader'] ?? 'Unknown',
                ];
            }
        } catch (\Exception $e) {
            // Silently fail, video info is optional
        }

        return null;
    }

    /**
     * Download video and convert to MP3
     */
    private function downloadAndConvert(string $url, string $outputPath, ?string $browser, ?string $cookiesFile): array
    {
        // Output template
        $outputTemplate = $outputPath . '/%(title)s.%(ext)s';

        $args = array_merge(
            ['yt-dlp'],
            $this->buildCookiesArgs($browser, $cookiesFile),
            [
                '-x',                                   // Extract audio
                '--audio-format', 'mp3',                // Convert to mp3
                '--audio-quality', '0',                 // Best audio quality
                '--embed-thumbnail',                    // Embed thumbnail
                '--add-metadata',                       // Add metadata
                '--no-playlist',                        // Don't download playlist
                '--restrict-filenames',                 // Restrict filenames
                '--extractor-args', 'youtube:player_client=android,web', // Use android client
                '--no-warnings',                        // Suppress warnings
                '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                '-o', $outputTemplate,                  // Output template
                '--progress',                           // Show progress
                '--newline',                            // New line for progress
                $url
            ]
        );

        $process = new Process($args);
        $process->setTimeout(600); // 10 minutes timeout

        $filename = '';
        $progressBar = $this->output->createProgressBar(100);
        $progressBar->setFormat(' %current%% [%bar%] %message%');
        $progressBar->setMessage('Đang khởi tạo...');
        $progressBar->start();

        try {
            $process->run(function ($type, $buffer) use ($progressBar, &$filename) {
                // Parse progress from yt-dlp output
                if (preg_match('/\[download\]\s+(\d+\.?\d*)%/', $buffer, $matches)) {
                    $percent = (int) $matches[1];
                    $progressBar->setProgress(min($percent, 100));
                    $progressBar->setMessage('Đang tải...');
                }

                // Parse filename from various output formats
                if (preg_match('/\[ExtractAudio\] Destination: (.+\.mp3)/', $buffer, $matches)) {
                    $filename = basename(trim($matches[1]));
                }
                
                if (preg_match('/Destination: (.+\.mp3)/', $buffer, $matches)) {
                    $filename = basename(trim($matches[1]));
                }

                // Conversion phase
                if (str_contains($buffer, 'ExtractAudio')) {
                    $progressBar->setMessage('Đang chuyển đổi...');
                }

                if (str_contains($buffer, 'EmbedThumbnail')) {
                    $progressBar->setMessage('Đang thêm thumbnail...');
                }
                
                if (str_contains($buffer, 'Metadata')) {
                    $progressBar->setMessage('Đang thêm metadata...');
                }
            });

            $progressBar->setProgress(100);
            $progressBar->setMessage('Hoàn thành!');
            $progressBar->finish();

            if ($process->isSuccessful()) {
                // Try to find the file if filename wasn't captured
                if (empty($filename)) {
                    $files = glob($outputPath . '/*.mp3');
                    if (!empty($files)) {
                        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                        $filename = basename($files[0]);
                    }
                }

                return [
                    'success' => true,
                    'filename' => $filename ?: 'File đã được tạo trong thư mục output'
                ];
            }

            $errorOutput = $process->getErrorOutput();
            
            return [
                'success' => false,
                'error' => $errorOutput ?: 'Lỗi không xác định'
            ];

        } catch (ProcessFailedException $e) {
            $progressBar->finish();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $progressBar->finish();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}