<?php

namespace Modules\Muasamcong\Livewire;

use Livewire\Component;
use App\Services\MuaSamCongService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SearchHsmt extends Component
{
    public $keyword = 'thuốc generic';
    public $from_date = '';
    public $to_date = '';
    public $results = [];
    public $total = 0;
    public $loading = false;
    public $error = '';
    public $selected = [];
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = collect($this->results)
                ->pluck('notifyNo')
                ->filter()
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function search(MuaSamCongService $service)
    {
        $this->loading = true;
        $this->error = '';

        if (!$this->from_date || !$this->to_date) {
            $this->error = "Bạn phải chọn Từ ngày và Đến ngày";
            $this->loading = false;
            return;
        }

        try {
            $fromIso = Carbon::parse($this->from_date)->startOfDay()->toISOString();
            $toIso   = Carbon::parse($this->to_date)->endOfDay()->toISOString();
        } catch (\Exception $e) {
            $this->error = "Ngày không hợp lệ: " . $e->getMessage();
            $this->loading = false;
            return;
        }

        $payload = [
            [
                "pageNumber" => "0",
                "query" => [
                    [
                        "index"        => "es-contractor-selection",
                        "keyWord"      => $this->keyword,
                        "matchType"    => "any-0",
                        "matchFields"  => ["notifyNo", "bidName"],
                        "filters"      => [
                            [
                                "fieldName"   => "publicDate",
                                "searchType"  => "range",
                                "from"        => $fromIso,
                                "to"          => $toIso
                            ],
                            [
                                "fieldName"   => "isDomestic",
                                "searchType"  => "in",
                                "fieldValues" => [1]
                            ],
                            [
                                "fieldName"   => "type",
                                "searchType"  => "in",
                                "fieldValues" => ["es-notify-contractor"]
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $result = $service->searchSmartV2($payload);

        if (($result['status'] ?? 0) !== 200) {
            $this->error = "API lỗi: " . ($result['raw'] ?? 'Không rõ');
            $this->loading = false;
            return;
        }

        $data = $result['data'] ?? null;
        $this->total = $data['page']['totalElements'] ?? 0;
        $this->results = $data['page']['content'] ?? [];

        // reset lựa chọn
        $this->selectAll = false;
        $this->selected = [];

        $this->loading = false;
    }

    public function exportExcel()
    {
        if (empty($this->selected)) {
            $this->error = "Bạn phải chọn ít nhất 1 dòng để xuất Excel.";
            return;
        }

        $data = collect($this->results)
            ->whereIn('notifyNo', $this->selected)
            ->map(function ($item) {
                return [
                    'Tên gói thầu' => $item['bidName'][0] ?? '',
                    'Mã TBMT'       => $item['notifyNo'] ?? '',
                    'Ngày đăng tải' => $item['publicDate'] ?? '',
                    'Đóng thầu'     => $item['bidOpenDate'] ?? '',
                    'Bên mời thầu'  => $item['investorName'] ?? '',
                    'Tỉnh'          => $item['locations'][0]['provName'] ?? '',
                ];
            })
            ->toArray();

        $fileName = 'hsmt_export_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\HsmtExport($data), $fileName);
    }

    public function render()
    {
        return view('Muasamcong::livewire.search-hsmt');
    }
}
