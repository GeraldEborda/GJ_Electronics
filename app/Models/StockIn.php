<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = ['supplier_id', 'employee_id', 'date_received', 'delivery_receipt_no', 'remarks'];

    protected $casts = ['date_received' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function details()
    {
        return $this->hasMany(StockInDetail::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->details->sum('total_amount');
    }

    public function getStockInCodeAttribute()
    {
        return 'SI-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}