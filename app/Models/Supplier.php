<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['supplier_name', 'contact_person', 'contact_info', 'address'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }
}