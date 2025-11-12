<?php

namespace Modules\Menu\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\BirthdayWish;

use App\Models\AlertUser;
use Illuminate\Support\Carbon;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(1);
  
        $messages["hi"] = "Hey, Happy Birthday {$user->name}";
        $messages["wish"] = "On behalf of the entire company I wish you a very happy birthday and send you my best wishes for much happiness in your life.";
          
        $user->notify(new BirthdayWish($messages));
  
        dd('Done');
    }
    
   

    public function getNotificationsData(Request $request)
    {
        $user = $request->user();

        $notifications = AlertUser::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->get();

        $dropdownHtml = '';
        foreach ($notifications as $key => $alert) {
            $icon = "<i class='mr-2 fas fa-bell'></i>";
            $time = "<span class='float-right text-muted text-sm'>"
                    . $alert->created_at->diffForHumans()
                    . "</span>";

            $dropdownHtml .= "<a href='#' class='dropdown-item'>
                                {$icon}{$alert->title}{$time}
                              </a>";

            if ($key < $notifications->count() - 1) {
                $dropdownHtml .= "<div class='dropdown-divider'></div>";
            }
        }

        return [
            'label' => $notifications->count(),
            'label_color' => 'danger',
            'icon_color' => 'dark',
            'dropdown' => $dropdownHtml,
        ];
    }
    
    public function getLanguageData(Request $request){
        $notifications = [
            [
                'icon' => 'fas fa-fw fa-envelope',
                'text' => ' Tiếng Việt',
            ],
            [
                'icon' => 'fas fa-fw fa-users text-primary',
                'text' => 'English',
           
            ],           
        ];

        $dropdownHtml = '';

        foreach ($notifications as $key => $not) {
            $icon = "<i class='mr-2 {$not['icon']}'></i>";

            $time = "<span class='float-right text-muted text-sm'>
                    {$not['time']}
                    </span>";

            $dropdownHtml .= "<a href='#' class='dropdown-item'>
                                {$icon}{$not['text']}{$time}
                            </a>";

            if ($key < count($notifications) - 1) {
                $dropdownHtml .= "<div class='dropdown-divider'></div>";
            }
        }

        return [
            'label' => 'Ngôn Ngữ',
            'label_color' => 'danger',
            'icon_color' => 'dark',
            'dropdown' => $dropdownHtml,
        ];
        
    }
}
