<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertYmdToMdy')) {
    function convertYmdToMdy($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format('m-d-Y');
    }
}

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertMdyToYmd')) {
    function convertMdyToYmd($date)
    {
        return Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
    }
}

function generateMenuJson()
{
    $menu = [
        ['header' => 'ACCOUNT SETTINGS'],
        [
            'text' => 'Manage Users',
            'url' => 'admin/users',
            'icon' => 'fas fa-fw fa-user',
            'can' => 'admin-create',
        ],
        [
            'text' => 'Manage Role',
            'url' => 'admin/roles',
            'icon' => 'fas fa-fw fa-user-shield',
            'can' => 'role-list',
        ],
        ['header' => 'MANAGER SETTINGS'],
        [
            'text' => 'Manager Settings',
            'url' => '#',
            'icon' => 'fas fa-fw fa-user',
            'can' => 'settings-list',
            'submenu' => [
                [
                    'text' => 'Cài đặt hệ thống',
                    'url' => 'settings',
                    'icon' => 'fas fa-fw fa-user',
                    'can' => 'settings-list',
                    'submenu' => [
                        [
                            'text' => 'Hướng dẫn sử dụng Admin',
                            'url' => 'settings/help',
                            'icon' => 'fas fa-fw fa-user',
                            'can' => 'settings-list',
                        ]
                    ]
                ],
                [
                    'text' => 'Hướng dẫn sử dụng Admin',
                    'url' => 'settings/help',
                    'icon' => 'fas fa-fw fa-user',
                    'can' => 'settings-list',
                ],
            ],
        ]
    ];

    // Chuyển đổi mảng thành JSON
    $jsonMenu = json_encode($menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Lưu file JSON vào thư mục public
    //$filePath = public_path('menu.json');
    $filePath = config_path('menu.json');
    File::put($filePath, $jsonMenu);

    return response()->json(['message' => 'Menu JSON created successfully!', 'path' => url('menu.json')]);
}

function loadMenuFromJson($filePath)
{
    // Kiểm tra xem file có tồn tại không
    if (!file_exists($filePath)) {
        throw new Exception("File not found: " . $filePath);
    }

    // Đọc nội dung file
    $jsonContent = file_get_contents($filePath);

    // Chuyển đổi JSON thành mảng
    $menuArray = json_decode($jsonContent, true);

    // Kiểm tra xem có lỗi trong việc chuyển đổi không
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON decode error: " . json_last_error_msg());
    }

    return $menuArray;
}

function getCategories($taxonomy)
{
    return DB::table('wp_terms')
        ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
        ->where('wp_term_taxonomy.taxonomy', $taxonomy)
        ->select('wp_terms.term_id', 'wp_terms.name', 'wp_term_taxonomy.parent')
        ->get();
}

if (! function_exists('convertYmdToDmy')) {
    function convertYmdToDmy($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y');
    }
}
  
/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertMdyToYmd')) {
    function convertMdyToDmy($date)
    {
        return Carbon::createFromFormat('m-d-Y', $date)->format('d-m-Y');
    }
}

if (! function_exists('lang_label')) {
    function lang_label()
    {
        return app()->getLocale() == 'vi' ? '🇻🇳 VI' : '🇺🇸 EN';
    }
}
