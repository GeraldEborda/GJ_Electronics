<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['sales_transaction_id', 'amount_paid', 'payment_method', 'status'];

    public function salesTransaction()
    {
        return $this->belongsTo(SalesTransaction::class);
    }
}