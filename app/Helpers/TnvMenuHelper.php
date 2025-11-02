<?php

namespace App\Helpers;


class TnvMenuHelper
{
   
    public static function loadMenuSidebar()
    {
        // Kiểm tra xem file có tồn tại không
        $filePath = base_path('Modules/Menu/menu.json') ?? config_path('menu.json');
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

        // $headerSidebar =  [
        //     'type' => 'sidebar-menu-search',
        //     'text' => 'search',
        // ];

        return [
        //    $headerSidebar,
            ...$menuArray,
        ];
    }

    public static function loadMenuNavbar()
    {
        return [    
            [
                'type' => 'navbar-search',
                'text' => 'search',
                'topnav_right' => true,
                'url' => 'navbar/search',
                'method' => 'post',
                'input_name' => 'searchVal',
                'id' => 'navbarSearch'
            ],
            [
                'type' => 'fullscreen-widget',
                'topnav_right' => true,
            ],  
            [
                'type' => 'navbar-notification',
                'id' => 'my-notification',
                'icon' => 'fas fa-bell',
                'url' => env('APP_URL') . '/notifications/show',
                'topnav_right' => true,
                'dropdown_mode' => true,
                'dropdown_flabel' => 'All notifications',
                'update_cfg' => [
                    'url' => env('APP_URL') . '/notifications/get',
                    'period' => 30,
                ],
                'can' => 'user-list',
            ],
            [
                'text' => 'Profile',
                'url' => 'admin/profile',
                'icon' => 'fas fa-fw fa-user',
                'topnav_user' => true,
                'can' => 'admin-list',
            ],
            [
                'text' => 'Settings',
                'url' => 'admin/settings',
                'icon' => 'fas fa-fw fa-cog',
                'topnav_user' => true,
                'icon_color' => 'primary',
                'can' => 'user-list',
            ],  
            
        ];
    }

    public static function showMenu($isSidebar = true, $isNavbar= true){       
      
        $menuSidebar = [];$menuNavbar =[];
        if($isSidebar){
            $menuSidebar = self::loadMenuSidebar();
        }
        if($isNavbar){
            $menuNavbar =self::loadMenuNavbar();
        }
        
      
        return  [    
            // Navbar items:        
            ...$menuNavbar,
            // Sidebar items:
            ...$menuSidebar
        ];
    }
  
}
