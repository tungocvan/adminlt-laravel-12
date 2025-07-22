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
        return view('livewire.user-search');
    }
      
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $data = User::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->take(10)
                    ->get();
      
        return response()->json($data);
    }
}