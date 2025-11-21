<?php

namespace Modules\Muasamcong\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\MuaSamCongService;
use Illuminate\Http\Request;

class MuasamcongController extends Controller
{
   public function index()
    {
        return response()->json([
            'status' => 'Api Muasamcong success',            
        ]);
    }  
    public function searchPricing(Request $request, MuaSamCongService $service)
    {
        // $validated = $request->validate([            
        //     'payload' => 'required|array',
        // ]);
        $payload = $request->all();      
        return $service->searchPricing(            
            $payload
        );
    }
}
 