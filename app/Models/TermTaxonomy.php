<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    protected $table = 'term_taxonomy';

    protected $fillable = ['term_id', 'taxonomy', 'parent_id', 'description', 'count'];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function parent()
    {
        return $this->belongsTo(TermTaxonomy::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(TermTaxonomy::class, 'parent_id')->with('children');
    }

}
