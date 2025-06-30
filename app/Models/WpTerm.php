<?php
// app/Models/Term.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpTerm extends Model
{
    public $timestamps = false;
    protected $table = 'wp_terms';
    protected $primaryKey = 'term_id';
    protected $fillable = [
        'name', 'slug', 'term_group'
    ];

}