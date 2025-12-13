<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KpiSalesMonthly extends Model
{
    use HasFactory;

    protected $table = 'kpi_sales_monthly';

    protected $fillable = [
        'year',
        'month',
        'product_type_id',
        'total_revenue',
        'total_transactions',
        'target_revenue',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'target_revenue' => 'decimal:2',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
