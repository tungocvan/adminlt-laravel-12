<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function taxonomy()
    {
        return $this->hasOne(TermTaxonomy::class, 'term_id');
    }
}
