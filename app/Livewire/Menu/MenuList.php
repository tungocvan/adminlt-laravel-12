<?php

namespace App\Livewire\Menu;

use Livewire\Component;
use Symfony\Component\Process\Process;

class MenuList extends Component
{
    public $menuItems = [];
    public $menuText;
    public $menuUrl;
    public $menuIcon;
    public $menuCan;
    public $menuHeader;
    public $showModal = false;
    public $currentMenuText;
    public $addMenu = false;
    public $addSubmenu = false;
    public $showMenuModal = false;
    public $actionMenu = '';
    public $backupFiles = [];
    public $nameJson = 'menu-backup';
    public $filePath;
    public $permissionError = null;
    public $isSaving = false;

    private $iconMenu = 'far fa-caret-square-right';

    /*-----------------------------------
     | Mount
     -----------------------------------*/
    public function mount()
    {
        $this->filePath = base_path('Modules/Menu/menu.json');

        // If file not exists, create an empty json file (safe)
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!file_exists($this->filePath)) {
            @file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        }

        // Load menu.json
        $this->menuItems = json_decode(@file_get_contents($this->filePath), true) ?? [];

        // Permission check
        if (!is_writable($dir) || !is_writable($this->filePath)) {
            $this->permissionError = "⚠️ Thư mục hoặc file <code>menu.json</code> chưa có quyền ghi!<br>"
                . "Hãy chạy lệnh (trong máy dev hoặc server có quyền):<br>"
                . "<code>sudo chown -R www-data:www-data {$dir} && sudo chmod -R 775 {$dir}</code>";
        }
    }

    /*-----------------------------------
     | Render
     -----------------------------------*/
    public function render()
    {
        return view('livewire.menu.menu-list');
    }

    /*-----------------------------------
     | UI helpers
     -----------------------------------*/
    public function addItem()
    {
        $this->addMenu = true;
        $this->showModal = true;
    }

    public function addItemSubmenu()
    {
        $count = count($this->menuItems) + 1;
        $subMenu = [
            'text' => "New Item SubMenu {$count}",
            'url' => '#',
            'icon' => $this->iconMenu,
            'can' => 'admin-list',
            'submenu' => [
                [
                    'text' => 'New Item menu',
                    'url' => '#',
                    'icon' => $this->iconMenu,
                    'can' => 'admin-list',
                ]
            ]
        ];

        $this->menuItems[] = $subMenu;
        $this->saveMenuJson('SubMenu created');
        $this->redirectRoute('menu.index');
    }

    public function addSubMenuByIndex($index, $key = null)
    {
        $submenu = [
            'text' => 'New Item SubMenu',
            'url' => '#',
            'icon' => $this->iconMenu,
            'can' => 'admin-list'
        ];

        if (!isset($this->menuItems[$index]['submenu'])) {
            $this->menuItems[$index]['submenu'] = [];
        }

        $this->menuItems[$index]['submenu'][] = $submenu;

        $this->saveMenuJson('SubMenu added');
        $this->redirectRoute('menu.index');
    }

    public function editItem($item)
    {
        // item can be json-encoded string
        $data = is_string($item) ? json_decode($item, true) : $item;
        $this->addMenu = false;
        $this->addSubmenu = false;

        if (isset($data['header'])) {
            $this->menuHeader = $data['header'];
            $this->menuCan = $data['can'] ?? '';
        } else {
            $this->menuHeader = null;
            $this->menuText = $data['text'] ?? '';
            $this->menuUrl = $data['url'] ?? '';
            $this->menuIcon = $data['icon'] ?? '';
            $this->menuCan = $data['can'] ?? '';
        }

        $this->currentMenuText = $data['text'] ?? $data['header'] ?? null;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['menuHeader', 'menuText', 'menuUrl', 'menuIcon', 'menuCan', 'showModal', 'addMenu']);
    }

    public function updateMenu()
    {
        if ($this->addMenu) {
            $newItem = [
                'text' => $this->menuText ?? 'New Item',
                'url' => $this->menuUrl ?? null,
                'icon' => $this->menuIcon ?? $this->iconMenu,
                'can' => $this->menuCan ?? 'admin-list',
            ];

            if (!$newItem['url']) {
                $this->menuItems[] = ['header' => $newItem['text'], 'can' => $newItem['can']];
            } else {
                $this->menuItems[] = $newItem;
            }
        } else {
            foreach ($this->menuItems as &$item) {
                if (isset($item['header']) && $item['header'] === $this->currentMenuText) {
                    $item['header'] = $this->menuHeader ?? $item['header'];
                    $item['can'] = $this->menuCan ?? $item['can'];
                }

                if (isset($item['submenu'])) {
                    foreach ($item['submenu'] as &$submenu) {
                        if ($submenu['text'] === $this->currentMenuText) {
                            $submenu['text'] = $this->menuText;
                            $submenu['url'] = $this->menuUrl;
                            $submenu['icon'] = $this->menuIcon;
                            $submenu['can'] = $this->menuCan;
                        }
                    }
                }

                if (isset($item['text']) && $item['text'] === $this->currentMenuText) {
                    $item['text'] = $this->menuText;
                    $item['url'] = $this->menuUrl;
                    $item['icon'] = $this->menuIcon;
                    $item['can'] = $this->menuCan;
                }
            }
            unset($item);
        }

        $this->saveMenuJson('Menu updated successfully');
        $this->showModal = false;
        $this->redirectRoute('menu.index');
    }

    /*-----------------------------------
     | Duplicate / Delete items
     -----------------------------------*/
    public function duplicateItem($itemJson, $index, $key = null)
    {
        $item = json_decode($itemJson, true);
        $newItem = [];

        if (isset($item['header'])) {
            $newItem['header'] = $item['header'] . ' (Copy)';
            $newItem['can'] = $item['can'] ?? null;
        } else {
            $newItem = $item;
            // ensure uniqueness in text
            $newItem['text'] = ($item['text'] ?? 'Copy') . ' (Copy)';
        }

        if ($key !== null && isset($this->menuItems[$index]['submenu'])) {
            array_splice($this->menuItems[$index]['submenu'], $key + 1, 0, [$newItem]);
        } else {
            array_splice($this->menuItems, $index + 1, 0, [$newItem]);
        }

        $this->saveMenuJson('Item duplicated successfully.');
        $this->redirectRoute('menu.index');
    }

    public function deleteItem($index, $key = null)
    {
        if ($key !== null && isset($this->menuItems[$index]['submenu'][$key])) {
            array_splice($this->menuItems[$index]['submenu'], $key, 1);
            // clean empty submenu
            if (empty($this->menuItems[$index]['submenu'])) {
                unset($this->menuItems[$index]['submenu']);
            }
        } elseif (isset($this->menuItems[$index])) {
            array_splice($this->menuItems, $index, 1);
        }

        $this->saveMenuJson('Item removed');
        $this->redirectRoute('menu.index');
    }

    /*-----------------------------------
     | Move up / down
     -----------------------------------*/
    public function moveUp($index, $key = null)
    {
        // When Blade passes strings (from interpolation), cast to int to be safe
        $index = is_numeric($index) ? (int) $index : $index;
        $key = is_numeric($key) ? (int) $key : $key;

        if ($key !== null) {
            if ($key > 0) {
                $this->swapItems($key, $key - 1, $index);
            }
        } else {
            if ($index > 0) {
                $this->swapItems($index, $index - 1);
            }
        }
    }

    public function moveDown($index, $key = null)
    {
        $index = is_numeric($index) ? (int) $index : $index;
        $key = is_numeric($key) ? (int) $key : $key;

        if ($key !== null) {
            if (isset($this->menuItems[$index]['submenu']) && $key < count($this->menuItems[$index]['submenu']) - 1) {
                $this->swapItems($key, $key + 1, $index);
            }
        } else {
            if ($index < count($this->menuItems) - 1) {
                $this->swapItems($index, $index + 1);
            }
        }
    }

    private function swapItems($indexA, $indexB, $index = null)
    {
        if ($index !== null) {
            $temp = $this->menuItems[$index]['submenu'][$indexA];
            $this->menuItems[$index]['submenu'][$indexA] = $this->menuItems[$index]['submenu'][$indexB];
            $this->menuItems[$index]['submenu'][$indexB] = $temp;
        } else {
            $temp = $this->menuItems[$indexA];
            $this->menuItems[$indexA] = $this->menuItems[$indexB];
            $this->menuItems[$indexB] = $temp;
        }

        $this->saveMenuJson('Đã thay đổi vị trí mục.');
        $this->redirectRoute('menu.index');
    }

    /*-----------------------------------
     | Backup / Restore menu.json
     -----------------------------------*/
    public function showMenu($action)
    {
        $this->actionMenu = $action;
        $this->showMenuModal = true;

        if ($action === 'restore') {
            $backupDir = base_path('Modules/Menu/menu');
            if (is_dir($backupDir)) {
                $this->backupFiles = array_values(array_diff(scandir($backupDir), ['..', '.']));
            } else {
                $this->backupFiles = [];
            }

            if (empty($this->backupFiles)) {
                session()->flash('message', 'Không có file backup để phục hồi.');
                $this->showMenuModal = false;
            }
        }
    }
    public function showCloseMenu()
    {
        $this->showMenuModal = false;
    }

    public function updateMenuJson()
    {
        $menuFilePath = $this->filePath;
        $backupDir = base_path('Modules/Menu/menu');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        if ($this->actionMenu === 'backup') {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupFileName = "{$this->nameJson}-{$timestamp}.json";
            $backupFilePath = "{$backupDir}/{$backupFileName}";

            if (copy($menuFilePath, $backupFilePath)) {
                session()->flash('message', "Backup created: {$backupFileName}");
            } else {
                session()->flash('error', 'Backup failed.');
            }

            $this->showMenuModal = false;
        }
    }

    public function restoreFile($fileName)
    {
        $backupDir = base_path('Modules/Menu/menu');
        $sourceFile = "{$backupDir}/{$fileName}";
        $destinationFile = $this->filePath;

        if (file_exists($sourceFile)) {
            if (copy($sourceFile, $destinationFile)) {
                session()->flash('message', "Menu restored from {$fileName}");
                $this->showMenuModal = false;
                $this->redirectRoute('menu.index');
            } else {
                session()->flash('error', 'Restore failed.');
            }
        } else {
            session()->flash('error', 'File không tồn tại.');
        }
    }

    public function deleteFile($fileName)
    {
        $backupDir = base_path('Modules/Menu/menu');
        $filePath = "{$backupDir}/{$fileName}";
        if (file_exists($filePath)) {
            unlink($filePath);
            $this->backupFiles = array_values(array_diff(scandir($backupDir), ['..', '.']));
        }
    }

    public function downloadFile($fileName)
    {
        $backupDir = base_path('Modules/Menu/menu');
        $filePath = "{$backupDir}/{$fileName}";

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            session()->flash('error', 'File không tồn tại.');
        }
    }

    /*-----------------------------------
     | Save menu.json with permission handling + spinner
     -----------------------------------*/
    public function saveMenuJson($title = '')
    {
        $this->isSaving = true;
        $filePath = $this->filePath;
        $directory = dirname($filePath);

        // If not writable, try to chown/chmod ONLY on non-production (safe guard)
        if ((!is_writable($directory) || (file_exists($filePath) && !is_writable($filePath))) && !app()->environment('production')) {
            try {
                $chown = new Process(['sudo', 'chown', '-R', 'www-data:www-data', $directory]);
                $chown->run();
                $chmod = new Process(['sudo', 'chmod', '-R', '775', $directory]);
                $chmod->run();
            } catch (\Throwable $e) {
                $this->isSaving = false;
                session()->flash('error', "Không thể phân quyền tự động: " . $e->getMessage());
                return;
            }
        }

        try {
            // write with lock
            file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
            session()->flash('message', $title ?: 'Lưu menu thành công!');
        } catch (\Throwable $e) {
            session()->flash('error', "Lỗi khi ghi file: " . $e->getMessage());
        }

        $this->isSaving = false;
    }
}
