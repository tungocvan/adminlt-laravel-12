<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class MenuEditor extends Component
{
        public $menu = [];
        public $showModal = false;
        public $newItem = [
            'text' => '',
            'url' => '',
            'icon' => '',
            'can' => '',
        ];

        public function mount()
            {
                $filePath = config_path('menu.json');

                // Đọc nội dung file JSON
                $this->menu = File::exists($filePath) ? json_decode(File::get($filePath), true) : [];
                
            }

        public function save()
        {
            
            $jsonMenu = json_encode($this->menu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $filePath = config_path('menu.json');
            File::put($filePath, $jsonMenu);

            session()->flash('success', 'Menu updated successfully!');
        }
        public function addItem()
        {
            // Thêm mục mới vào menu
            $this->menu[] = $this->newItem;
    
            // Reset lại giá trị của newItem
            $this->newItem = [
                'text' => '',
                'url' => '',
                'icon' => '',
                'can' => '',
            ];
        }

        public function addSubItem($index)
        {
            $this->menu[$index]['submenu'][] = [
                'text' => '',
                'url' => '',
                'icon' => '',
                'can' => ''
            ];
        }

        public function removeItem($index)
        {
            unset($this->menu[$index]);
            $this->menu = array_values($this->menu); // Để cập nhật chỉ số mảng
        }

        public function removeSubItem($itemIndex, $subItemIndex)
        {
            unset($this->menu[$itemIndex]['submenu'][$subItemIndex]);
            $this->menu[$itemIndex]['submenu'] = array_values($this->menu[$itemIndex]['submenu']);
        }


        public function moveUp($index)
        {
            if ($index > 0) {
                $temp = $this->menu[$index - 1];
                $this->menu[$index - 1] = $this->menu[$index];
                $this->menu[$index] = $temp;
            }
        }

        public function moveDown($index)
        {
            if ($index < count($this->menu) - 1) {
                $temp = $this->menu[$index + 1];
                $this->menu[$index + 1] = $this->menu[$index];
                $this->menu[$index] = $temp;
            }
        }
        public function moveSubItemUp($itemIndex, $subItemIndex)
    {
        if ($subItemIndex > 0) {
            $temp = $this->menu[$itemIndex]['submenu'][$subItemIndex - 1];
            $this->menu[$itemIndex]['submenu'][$subItemIndex - 1] = $this->menu[$itemIndex]['submenu'][$subItemIndex];
            $this->menu[$itemIndex]['submenu'][$subItemIndex] = $temp;
        }
    }

    public function moveSubItemDown($itemIndex, $subItemIndex)
    {
        if ($subItemIndex < count($this->menu[$itemIndex]['submenu']) - 1) {
            $temp = $this->menu[$itemIndex]['submenu'][$subItemIndex + 1];
            $this->menu[$itemIndex]['submenu'][$subItemIndex + 1] = $this->menu[$itemIndex]['submenu'][$subItemIndex];
            $this->menu[$itemIndex]['submenu'][$subItemIndex] = $temp;
        }
    }
    public function duplicateSubItem($itemIndex, $subItemIndex)
    {
        $subItem = $this->menu[$itemIndex]['submenu'][$subItemIndex];
        $this->menu[$itemIndex]['submenu'][] = $subItem; // Nhân bản mục
    }

}