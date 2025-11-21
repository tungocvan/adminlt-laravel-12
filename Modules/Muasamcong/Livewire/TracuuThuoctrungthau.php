<?php

namespace Modules\Muasamcong\Livewire;

use Livewire\Component;
use App\Services\MuaSamCongService;

class TracuuThuoctrungthau extends Component
{
    public $keyword = '';
    public $results = [];
    public $loading = false;
    public $error = '';

    public function search(MuaSamCongService $service)
    {
        if (!$this->keyword) {
            $this->error = "Vui lòng nhập từ khóa!";
            return;
        }

        $this->loading = true;
        $this->error = '';
        $this->results = [];

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
        
        try {
            $this->results = $service->searchPricing($payload);
           //  dd($this->results);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        $this->loading = false;
    }

    public function render()
    {
        return view('Muasamcong::livewire.tracuu-thuoctrungthau');
    }
}
