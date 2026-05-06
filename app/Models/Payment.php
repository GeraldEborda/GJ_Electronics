<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['sales_transaction_id', 'payment_date', 'amount_paid', 'payment_method_id', 'status', 'is_archived'];

    protected $casts = [
        'payment_date' => 'date',
        'is_archived' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function salesTransaction()
    {
        return $this->belongsTo(SalesTransaction::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
