<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = [
        'main_price',
        'price',
        'stock',
        'discount',
        'count',
        'max_sell',
        'sold',
        'status',
        'product_id',
        'color_id',
        'guaranty_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function guaranty()
    {
        return $this->belongsTo(Guaranty::class);
    }
}
