<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'code',
        'area_type',
        'name',
        'order_index',
        'status',
        'created_date',
        'created_by',
        'parent_code',
        'name_translate'
    ];

    protected $dates = ['created_date'];

    public function parent()
    {
        return $this->belongsTo(Area::class, 'parent_code', 'code');
    }

    public function children()
    {
        return $this->hasMany(Area::class, 'parent_code', 'code');
    }
}
