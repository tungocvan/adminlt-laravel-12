<?php

namespace Modules\Proposal\Http\Controllers\Api;
use App\Http\Controllers\Controller;


class ProposalController extends Controller
{
   public function index()
    {
        return response()->json([
            'status' => 'Api Proposal success',            
        ]);
    }  

}
