<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CrmLead extends Model
{
    use HasFactory;

    protected $table = 'crm_lead';
    protected $primaryKey = 'id_lead';

    protected $fillable = [
        'id_nasabah',
        'produk_minat',
        'sumber_lead',
        'status_lead',
        'tanggal_input',
    ];

    protected $casts = [
        'tanggal_input' => 'datetime',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah');
    }

    public function opportunity()
    {
        return $this->hasOne(Opportunity::class, 'id_lead', 'id_lead');
    }
}
