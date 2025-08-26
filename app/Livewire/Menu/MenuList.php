<?php

namespace App\Livewire\Menu;

use Livewire\Component;

class MenuList extends Component
{
    public $menuItems;
    public $menuText;
    public $menuUrl;
    public $menuIcon;
    public $menuCan;
    public $menuId;
    public $menuHeader;
    public $showModal = false;
    public $currentMenuText;
    public $addMenu = false;
    public $addSubmenu = false;
    public $showMenuModal = false;
    public $actionMenu = '';
    public $backupFiles = [];
    public $nameJson = 'menu-backup';
    private $iconMenu = 'far fa-caret-square-right';
    public function mount()
    {
        $filePath = config_path('menu.json');
        $this->menuItems = json_decode(file_get_contents($filePath), true);
    }

    public function render()
    {
        return view('livewire.menu.menu-list');
    }

    public function addItem()
    {
        $this->addMenu = true;
        $this->showModal = true; // Hiển thị modal
    }
    public function addItemSubmenu()
    {
        //dd($this->menuItems);
        $subMenu = [
            'text' => 'New Item SubMenu '. count($this->menuItems)+1,
            'url' => '#',
            'icon' => $this->iconMenu ,
            'can' => 'admin-list',
            'submenu' => [
                [
                    'text' => 'New Item menu',
                    'url' => '#',
                    'icon' => $this->iconMenu ,
                    'can' => 'admin-list',
                ]
            ]
        ];
        $this->menuItems[]= $subMenu;

        // $filePath = config_path('menu.json');
        // file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT)); 

        
        // Thông báo thành công
        // session()->flash('message', 'SubMenu updated successfully.');
        $this->saveMenuJson('SubMenu updated successfully.');
        $this->redirectRoute('settings.menu');
    }

    public function addSubMenuByIndex($index,$key=null){
        //dd($index,$key);
        $submenu = [
                'text' => 'New Item SubMenu ',
                'url' => '#',
                'icon' => $this->iconMenu ,
                'can' => 'admin-list'
        ];
        $this->menuItems[$index]['submenu'][] = $submenu;

        // $filePath = config_path('menu.json');
        // file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT));    
        
        // // Thông báo thành công
        // session()->flash('message', 'SubMenu updated successfully.');
        $this->saveMenuJson('SubMenu updated successfully.');
        $this->redirectRoute('settings.menu');
        //dd($this->menuItems[$index]);
    }

    public function editItem($item)
    {
        $this->addMenu = false;
        $this->addSubmenu = false;        
        $item = json_decode($item, true);
        //dd($item);
        if (isset($item['header'])) {
            $this->menuHeader = $item['header'];
            $this->menuCan = $item['can'] ?? '';
            // $this->menuUrl = ''; 
            // $this->menuIcon = ''; 
            // $this->menuCan = ''; 
        } else {
            $this->menuHeader = null;
            $this->menuText = $item['text'] ?? '';
            $this->menuUrl = $item['url'] ?? '';
            $this->menuIcon = $item['icon'] ?? '';
            $this->menuCan = $item['can'] ?? '';
        }

        $this->currentMenuText = $item['text'] ?? $item['header'] ; // Lưu trữ tên menu hiện tại
        $this->showModal = true; // Hiển thị modal
    }

    
    public function closeModal()
    {
            $this->menuHeader = null; 
            $this->menuText = null;
            $this->menuUrl = null;
            $this->menuIcon =null;
            $this->menuCan = null;
            $this->showModal = false; // Đóng modal
    }
    
    public function updateMenu()
        {
           
            if($this->addMenu){
                $newItem = [];
                $newItem['text'] = $this->menuText ?? 'new item';
                $newItem['url'] = $this->menuUrl ?? null;
                $newItem['icon'] = $this->menuIcon ?? $this->iconMenu ;
                $newItem['can'] = $this->menuCan ?? 'admin-list';
                if($newItem['url'] ==null){
                    $this->menuItems[]['header'] = $newItem['text'];
                }else{
                    $this->menuItems[] = $newItem;
                }                    
                      
            }else{
                // Tìm vị trí của mục menu cần cập nhật        
                foreach ($this->menuItems as &$item) {
                    // Kiểm tra nếu mục là header
            
                    if (isset($item['header']) && $item['header'] === $this->currentMenuText) {
                        // Cập nhật header nếu cần
                        $item['header'] = $this->menuHeader ?? $item['header']; // Nếu bạn muốn cập nhật, hãy đảm bảo có giá trị
                        $item['can'] = $this->menuCan ?? $item['can'];
                    // return; // Thoát sau khi cập nhật
                    }
                    

                    // Kiểm tra nếu mục có submenu
                    if (isset($item['submenu'])) {
                        foreach ($item['submenu'] as &$submenu) {
                            if ($submenu['text'] === $this->currentMenuText) {
                                $submenu['text'] = $this->menuText;
                                $submenu['url'] = $this->menuUrl;
                                $submenu['icon'] = $this->menuIcon;
                                $submenu['can'] = $this->menuCan;
                                // return; // Thoát sau khi cập nhật
                            }
                        }
                    }

                    // Kiểm tra nếu mục là mục chính không có submenu
                    if (isset($item['text']) && $item['text'] === $this->currentMenuText) {
                        $item['text'] = $this->menuText;
                        $item['url'] = $this->menuUrl;
                        $item['icon'] = $this->menuIcon;
                        $item['can'] = $this->menuCan;
                    }
                }
            }
            
            
            
            //dd('$this->menuItems');
            // Cập nhật file menu.json
            

            // $filePath = config_path('menu.json');
            // file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT)); 

            
            // // Thông báo thành công
            // session()->flash('message', 'Menu updated successfully.');
            // Đóng modal sau khi cập nhật
            $this->saveMenuJson('menu updated successfully');
            $this->showModal  = false;
            $this->redirectRoute('settings.menu');

        }
    public function duplicateItem($itemJson, $index, $key=null)
    {
        $newItem = [];            
        $countSubmenu= '';
        $item = json_decode($itemJson, true);
        // dd($index);
        // $newItem = $item;
        if (isset($item['header'])) {
            $newItem['header'] = $item['header']. ' (Copy)';
        }
        else{

            if (isset($this->menuItems[$index]['submenu'])) {
                $countSubmenu =count($this->menuItems[$index]['submenu']) +1;
            }


            $newItem['text'] = $item['text'] .' ' .  $countSubmenu; // Change text for uniqueness
            $newItem['url'] =  $item['url'] ;
            $newItem['icon'] = $item['icon'] ;
            $newItem['can'] =  $item['can'] ;

            // if (isset($item['submenu'])) {
            //     foreach ($item['submenu'] as $submenu) {
            //             $newItem['submenu']['text'] = $item['submenu']['text'];
            //             $newItem['submenu']['url'] = $item['submenu']['url'];
            //             $newItem['submenu']['icon'] = $item['submenu']['url'];
            //             $newItem['submenu']['can'] = $item['submenu']['can'];
            //     }
            // }
        }
        
        
        
        // Add the new item to the menuItems array
        if($key !== null){
            array_splice($this->menuItems[$index]['submenu'],$key+1,0,[$newItem]);
        }else{
            array_splice($this->menuItems,$index+1,0,[$newItem]);

        }
        
        // Cập nhật file menu.json
       

        // $filePath = config_path('menu.json');
        // file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT));
        // // Optionally, you can add a success message
        // session()->flash('message', 'Item duplicated successfully.');
        $this->saveMenuJson('Item duplicated successfully.');
        $this->redirectRoute('settings.menu');
    }

    public function deleteItem($index, $key=null) {
        
        if($key !== null){
            //dd($this->menuItems[$index]['submenu']);
            array_splice($this->menuItems[$index]['submenu'],$key,1);
        }else{
            array_splice($this->menuItems,$index,1);
        }
        // Cập nhật file menu.json
        // $filePath = config_path('menu.json');
        // file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT));
        // // Optionally, you can add a success message
        // session()->flash('message', 'Item duplicated successfully.');
        $this->saveMenuJson('Item duplicated successfully.');
        $this->redirectRoute('settings.menu');
    }

    public function moveUp($index, $key=null)
    {
        if($key !== null){
            //dd($key);
            $this->swapItems($key, $key - 1,$index);
        }else{
            if ($index > 0) {
                // Di chuyển mục lên một vị trí
                $this->swapItems($index, $index - 1);
            }
        }
        
    }
    
    public function moveDown($index, $key=null)
    {
        if($key !== null){
            $this->swapItems($key, $key+1,$index);
        }else{
            if ($index < count($this->menuItems) - 1) {
                // Di chuyển mục xuống một vị trí
                $this->swapItems($index, $index + 1);
            }
        }
        
    }
    
    private function swapItems($indexA, $indexB,$index=null)
    {
        // Hoán đổi vị trí của hai mục
        if($index !== null){
            $temp = $this->menuItems[$index]['submenu'][$indexA];
            $this->menuItems[$index]['submenu'][$indexA] = $this->menuItems[$index]['submenu'][$indexB];
            $this->menuItems[$index]['submenu'][$indexB] = $temp;
        }else{
            $temp = $this->menuItems[$indexA];
            $this->menuItems[$indexA] = $this->menuItems[$indexB];
            $this->menuItems[$indexB] = $temp;
        }
    
        
            // Cập nhật file menu.json
        // $filePath = config_path('menu.json');
        // file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT));
        //     // Thông báo thành công
        // session()->flash('message', 'Đã thay đổi vị trí mục.');
        $this->saveMenuJson('Đã thay đổi vị trí mục.');
        $this->redirectRoute('settings.menu');

    }


    public function showMenu($action){
        $this->actionMenu = $action;
        $this->showMenuModal = true;
        if($this->actionMenu === 'restore') {
            $backupDir = config_path('menu');
            $this->backupFiles = array_diff(scandir($backupDir), ['..', '.']);
            if(count($this->backupFiles) == 0){
                session()->flash('message', 'No have restore file.');
                $this->redirectRoute('settings.menu');
                
            }
            
            //dd(count($this->backupFiles));
            //$this->dispatchBrowserEvent('showRestoreModal', ['files' => $backupFiles]);
           // dd($this->backupFiles);
        }
    }
    public function showCloseMenu(){
        $this->showMenuModal = false;
    }

    public function updateMenuJson()
    {
       // dd($this->actionMenu);         
        $menuFilePath = config_path('menu.json');
        $backupDir = config_path('menu');
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        if ($this->actionMenu === 'backup') {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupFileName = $this->nameJson."-{$timestamp}.json";
            $backupFilePath = $backupDir . '/' . $backupFileName;

            if (copy($menuFilePath, $backupFilePath)) {
                session()->flash('message', 'Backup created successfully: ' . $backupFileName);
                $this->showMenuModal = false;                
            } else {
                session()->flash('message', 'Failed to create backup.');
            }
        }
    }

    public function restoreFile($fileName)
    {
        $backupDir = config_path('menu');
        $sourceFile = $backupDir . '/' . $fileName;
        $destinationFile = config_path('menu.json');

        if (copy($sourceFile, $destinationFile)) {
            session()->flash('message', 'Menu restored successfully from ' . $fileName);
            $this->showMenuModal = false;
            $this->redirectRoute('settings.menu');
        } else {
            session()->flash('message', 'Failed to restore menu.');
        }
    }

    public function deleteFile($fileName)
    {
        $backupDir = config_path('menu');
        $filePath = $backupDir . '/' . $fileName;
     
        if (unlink($filePath)) {
            $this->backupFiles = array_diff(scandir($backupDir), ['..', '.']);
            //session()->flash('message', 'Backup file deleted successfully: ' . $fileName);
            //$this->showMenuModal = true;
           
        } 
       
    }

    public function downloadFile($fileName)
    {
        $backupDir = config_path('menu');
        $filePath = $backupDir . '/' . $fileName;

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            session()->flash('message', 'File không tồn tại.');
        }
    }

    public function saveMenuJson($title='')
    {
        // Cập nhật file menu.json
        $filePath = config_path('menu.json');
        file_put_contents($filePath, json_encode($this->menuItems, JSON_PRETTY_PRINT));
        // Optionally, you can add a success message
        session()->flash('message', $title);
        // $this->redirectRoute('settings.menu');
    }

    

 }