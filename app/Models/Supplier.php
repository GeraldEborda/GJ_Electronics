<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['supplier_name', 'first_name', 'last_name', 'contact_info', 'address', 'is_archived'];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function getContactPersonAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
