<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use softDeletes;
    protected $fillable = [
        'name',
        'code'
    ];

    public function products(): BelongsToMany
    {
        return $this->BelongsToMany(Product::class, 'color_product', 'color_id', 'product_id');
    }
}
