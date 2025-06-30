<?php
// app/Models/TermTaxonomy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpTermTaxonomy extends Model
{
    protected $table = 'wp_term_taxonomy';
    public $timestamps = false;
    protected $fillable = [
        'term_id', 'taxonomy', 'description', 'parent', 'count'
    ];
}