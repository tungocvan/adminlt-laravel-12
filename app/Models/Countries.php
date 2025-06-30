<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
class Countries extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Một quốc gia có nhiều khách hàng
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
