<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'url',
        'icon',
        'can',
        'type',
        'parent_id',
        'description',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    /**
     * Danh mục cha
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Danh mục con
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Scope chỉ lấy menu
     */
    public function scopeMenu($query)
    {
        return $query->where('type', 'menu');
    }

    /**
     * Scope chỉ lấy category
     */
    public function scopeCategory($query)
    {
        return $query->where('type', 'category');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }


}
