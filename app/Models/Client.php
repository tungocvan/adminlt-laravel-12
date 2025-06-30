<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Countries;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'age', 'address', 'married', 'country_id'];

    protected $casts = [
        'married' => 'boolean',
    ];

    // Một khách hàng thuộc về một quốc gia
    public function countries()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }
}
