<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    protected $fillable = [
        'customer_id', 'employee_id', 'sales_date', 'total_amount', 'status'
    ];

    protected $casts = ['sales_date' => 'datetime'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(SalesDetail::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getSaleCodeAttribute()
    {
        return 'SALE-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}