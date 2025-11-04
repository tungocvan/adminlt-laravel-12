<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait Filterable
{
    /**
     * Scope filter toàn diện + meta, dùng cho bất kỳ model nào
     *
     * @param Builder $query
     * @param array $params
     * @param int $perPage
     * @return array ['data'=>[], 'meta'=>[]]
     */
    public function scopeFilter(Builder $query, array $params, int $perPage = 20): array
    {
        $params = array_filter($params, fn($v) => $v !== null && $v !== '');

        $table = $query->getModel()->getTable();

        // Hàm parse date chuẩn hóa dd/mm/yyyy -> yyyy-mm-dd
        $parseDate = function($date) {
            if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $date, $m)) return "{$m[3]}-{$m[2]}-{$m[1]}";
            if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $date)) return $date;
            return null;
        };

        // 1️⃣ Filter field bình thường (tùy chỉnh theo model)
        $normalFields = ['id','email','status','is_admin']; // bạn có thể mở rộng
        foreach ($normalFields as $field) {
            if (!empty($params[$field])) {
                is_array($params[$field])
                    ? $query->whereIn($field, $params[$field])
                    : $query->where($field, $params[$field]);
            }
        }

        // 2️⃣ Keyword search nếu model có scopeKeyword
        // Keyword search
        $keyword = $params['search'] ?? $params['keyword'] ?? null;
        if (!empty($keyword)) {
            $model = $query->getModel();
            if (method_exists($model, 'scopeKeyword')) {
                $model->scopeKeyword($query, $keyword);
            }
        }


        // 3️⃣ Filter tự động các field date/datetime từ $casts
        $dateFields = array_filter($query->getModel()->getCasts(), fn($type) => in_array($type, ['date','datetime']));
        foreach ($dateFields as $field => $type) {
            $columnType = Schema::getColumnType($table, $field);
            $isStringColumn = in_array($columnType, ['string','varchar','text']);

            $applyDate = function($q, $col, $date, $op='=') use ($isStringColumn) {
                if ($isStringColumn) $q->whereRaw("STR_TO_DATE($col, '%d/%m/%Y') $op ?", [$date]);
                else $q->whereDate($col, $op, $date);
            };

            // exact match
            if (!empty($params[$field])) { 
                $date = $parseDate($params[$field]); 
                if($date) $query->where(fn($q)=>$applyDate($q,$field,$date)); 
            }

            // from
            if (!empty($params[$field.'_from'])) { 
                $date = $parseDate($params[$field.'_from']); 
                if($date) $query->where(fn($q)=>$applyDate($q,$field,$date,'>=')); 
            }

            // to
            if (!empty($params[$field.'_to'])) { 
                $date = $parseDate($params[$field.'_to']); 
                if($date) $query->where(fn($q)=>$applyDate($q,$field,$date,'<=')); 
            }
        }

        // 4️⃣ Sắp xếp mặc định
        $query->orderBy('id','desc');

        // 5️⃣ Phân trang + meta
        $paginated = $query->paginate($perPage);
        return [
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
        ];
    }
}
