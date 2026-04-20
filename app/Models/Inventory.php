<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['product_id', 'current_stock', 'minimum_stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusAttribute()
    {
        if ($this->current_stock <= 0) return 'out_of_stock';
        if ($this->current_stock <= 5) return 'critical';
        if ($this->current_stock < $this->minimum_stock) return 'low_stock';
        return 'in_stock';
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'out_of_stock' => 'Out of Stock',
            'critical'     => 'Critical',
            'low_stock'    => 'Low Stock',
            default        => 'In Stock',
        };
    }
}
