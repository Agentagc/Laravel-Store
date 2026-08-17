<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use softDeletes;
    protected $fillable = [
        'name',
        'e_name',
        'slug',
        'image',
        'description',
        'price',
        'stock',
        'discount',
        'count',
        'max_sell',
        'viewed',
        'sold',
        'status',
        'category_id',
        'brand_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'color_product', 'product_id', 'color_id');
    }

    public function guaranties()
    {
        return $this->belongsToMany(Guaranty::class, 'guaranty_product', 'product_id', 'guaranty_id');
    }

}
