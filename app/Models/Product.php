<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['supplier_id', 'category_id', 'model_number', 'product_name', 'description', 'unit_price', 'is_archived'];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockInDetails()
    {
        return $this->hasMany(StockInDetail::class);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }
}
