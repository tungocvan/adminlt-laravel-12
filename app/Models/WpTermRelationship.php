<?php
// app/Models/TermRelationship.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WpTermRelationship extends Model
{
    protected $table = 'wp_term_relationships';
    public $timestamps = false;
    protected $fillable = [
        'object_id', 'term_taxonomy_id', 'term_order'
    ];
}