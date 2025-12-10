<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function kpiSalesMonthly()
    {
        return $this->hasMany(KpiSalesMonthly::class);
    }
}
