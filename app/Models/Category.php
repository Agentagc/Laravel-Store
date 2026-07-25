<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use softDeletes;
    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'parent_id'
    ];


    public function parentCategory() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id')->withDefault(['name' => 'دسته بندی اصلی']);
    }

    public function childCategory() : HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

}
