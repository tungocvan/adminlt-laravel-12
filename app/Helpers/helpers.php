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

if (! function_exists('renderCategoryTree')) {
    /**
     * Render category tree as nested checkboxes.
     *
     * @param \Illuminate\Support\Collection|array $categories  Collection of categories (ideally root nodes)
     * @param array $selectedCategories             Array of selected category ids
     * @param int $level
     * @param array|null &$rendered                Internal: track rendered ids to avoid duplicates
     * @return string
     */
    function renderCategoryTree($categories, $selectedCategories = [], $level = 0, &$rendered = null)
    {
        // init visited set
        if ($rendered === null) $rendered = [];

        $html = '';

        foreach ($categories as $category) {
            // skip if already rendered
            if (in_array($category->id, $rendered, true)) {
                continue;
            }

            // mark as rendered
            $rendered[] = $category->id;

            $margin = $level * 20;

            // checked attribute nếu id nằm trong selectedCategories
            $checked = in_array($category->id, $selectedCategories) ? 'checked' : '';

            $html .= '<div class="form-check" style="margin-left:'.$margin.'px">';
            $html .= '<input type="checkbox"
                        class="form-check-input"
                        wire:model="selectedCategories"
                        value="'.htmlspecialchars($category->id, ENT_QUOTES).'"
                        id="cat_'.htmlspecialchars($category->id, ENT_QUOTES).'" '.$checked.'>';
            $html .= '<label class="form-check-label" for="cat_'.htmlspecialchars($category->id, ENT_QUOTES).'">'
                     .htmlspecialchars($category->name, ENT_QUOTES).'</label>';
            $html .= '</div>';

            // nếu có children thì đệ quy
            if (!empty($category->children) && $category->children->count()) {
                $html .= renderCategoryTree($category->children, $selectedCategories, $level + 1, $rendered);
            }
        }

        return $html;
    }
}





if (! function_exists('renderCategoryRows')) {
    function renderCategoryRows($categories, $parentId = null, $prefix = '')
        {
            $html = '';

            foreach ($categories->where('parent_id', $parentId) as $category) {
                $html .= '<tr>';
                $html .= '<td>' . $category->id . '</td>';
                $html .= '<td>' . $prefix . e($category->name) . '</td>';
                $html .= '<td>' . e($category->slug) . '</td>';
                $html .= '<td><span class="badge badge-info">' . e($category->type) . '</span></td>';
                $html .= '<td>' . ($category->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-secondary">Inactive</span>') . '</td>';
                $html .= '<td>
                            <button wire:click="openEdit(' . $category->id . ')" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Sửa
                            </button>
                            <button wire:click="deleteCategory(' . $category->id . ')" onclick="return confirm(\'Xác nhận xóa?\')" class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i> Xóa
                            </button>
                        </td>';
                $html .= '</tr>';

                // Gọi đệ quy để render con
                $html .= renderCategoryRows($categories, $category->id, $prefix . '— ');
            }

            return $html;
        }
}
// if (! function_exists('cleanString')) {
//     function cleanString(?string $value): string
//     {
  
//         if ($value === null) return '';
//         $value = trim((string)$value);
//         $value = str_replace(["\n", "\r", "\t"], ' ', $value);
        
//         return preg_replace('/\s+/', ' ', $value);
//     }
// }


if (!function_exists('vn_number_to_words')) {
    function vn_number_to_words($number)
    {
        $dictionary  = [
            0 => 'không',
            1 => 'một',
            2 => 'hai',
            3 => 'ba',
            4 => 'bốn',
            5 => 'năm',
            6 => 'sáu',
            7 => 'bảy',
            8 => 'tám',
            9 => 'chín'
        ];

        $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ', 'tỷ tỷ'];

        if (!is_numeric($number)) {
            return false;
        }

        // Tránh lỗi số âm
        $number = abs($number);

        $result = '';
        $unitIndex = 0;

        do {
            $chunk = $number % 1000;
            $number = floor($number / 1000);

            if ($chunk > 0) {
                $chunkText = readThreeDigits($chunk, $dictionary);
                $result = $chunkText . ' ' . $units[$unitIndex] . ' ' . $result;
            }

            $unitIndex++;
        } while ($number > 0 && $unitIndex < count($units));

        $result = trim(preg_replace('/\s+/', ' ', $result));

        return ucfirst($result) . ' đồng';
    }

    function readThreeDigits($number, $dictionary)
    {
        $hundreds = floor($number / 100);
        $tens = floor(($number % 100) / 10);
        $ones = $number % 10;

        $text = '';

        if ($hundreds > 0) {
            $text .= $dictionary[$hundreds] . ' trăm';
            if ($tens == 0 && $ones > 0) {
                $text .= ' lẻ';
            }
        }

        if ($tens > 1) {
            $text .= ' ' . $dictionary[$tens] . ' mươi';
            if ($ones == 1) $text .= ' mốt';
            elseif ($ones == 4) $text .= ' tư';
            elseif ($ones == 5) $text .= ' lăm';
            elseif ($ones > 0) $text .= ' ' . $dictionary[$ones];
        } elseif ($tens == 1) {
            $text .= ' mười';
            if ($ones == 5) $text .= ' lăm';
            elseif ($ones > 0) $text .= ' ' . $dictionary[$ones];
        } elseif ($tens == 0 && $hundreds == 0 && $ones > 0) {
            $text .= $dictionary[$ones];
        } elseif ($tens == 0 && $ones > 0) {
            $text .= ' ' . $dictionary[$ones];
        }

        return trim($text);
    }
}
