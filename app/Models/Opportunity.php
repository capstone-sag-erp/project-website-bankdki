<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opportunity extends Model
{
    use HasFactory;

    protected $table = 'opportunity';
    protected $primaryKey = 'id_opportunity';

    protected $fillable = [
        'id_lead',
        'nilai_estimasi',
        'tahap',
        'tanggal_update',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
        'nilai_estimasi' => 'decimal:2',
    ];

    public function crmLead()
    {
        return $this->belongsTo(CrmLead::class, 'id_lead', 'id_lead');
    }
}
