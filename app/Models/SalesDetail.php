<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    protected $fillable = ['sales_transaction_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];

    public function salesTransaction()
    {
        return $this->belongsTo(SalesTransaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}