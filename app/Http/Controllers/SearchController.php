<?php
    
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
    
class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        return view('search');
    }
      
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $users = User::select("name", "id","email")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->take(10)
                    ->get();
        $data = $users->map(function ($user) {
            $html = $user->name . '<strong>(' . $user->email . ')'.'</strong>';// trả kết quả danh sách tìm được
            return [
                'label' => $html,
                'value' => $user->name,
                'email' => $user->email,
                'id' => $user->id,
            ];
        });
        return response()->json($data);
    }
}