<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInDetail extends Model
{
    protected $fillable = [
        'stock_in_id', 'product_id', 'quantity_received',
        'cost_per_unit', 'total_amount', 'condition_status'
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}