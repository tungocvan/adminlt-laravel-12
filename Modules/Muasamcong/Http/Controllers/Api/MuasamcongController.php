<?php

namespace Modules\Muasamcong\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\MuaSamCongService;
use Illuminate\Http\Request;

class MuasamcongController extends Controller
{
    public $keyword='';
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
       // $payload = $request->all();    
        $this->keyword = $request->keyword;
        $payload = [
            [
                "pageSize" => 20,
                "pageNumber" => 0,
                "query" => [
                    [
                        "index" => "es-smart-pricing",
                        "keyWord" => $this->keyword,
                        "keyWordNotMatch" => "",
                        "matchType" => "exact",
                        "matchFields" => ["ten_thuoc","ten_hoat_chat","ma_tbmt"],
                        "filters" => [
                            ["fieldName"=>"medicines","searchType"=>"in","fieldValues"=>["0"]],
                            ["fieldName"=>"type","searchType"=>"in","fieldValues"=>["HANG_HOA"]],
                            ["fieldName"=>"tab","searchType"=>"in","fieldValues"=>["THUOC_TAN_DUOC"]]
                        ]
                    ]
                ]
            ]
        ];
    
      
        return $service->searchPricing(            
            $payload
        );
    }
}
  