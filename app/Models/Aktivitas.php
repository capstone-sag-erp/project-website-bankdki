<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aktivitas extends Model
{
    use HasFactory;

    protected $table = 'aktivitas';
    protected $primaryKey = 'id_aktivitas';

    protected $fillable = [
        'id_tiket',
        'id_agent',
        'jenis_aktivitas',
        'tanggal_aktivitas',
        'catatan',
    ];

    protected $casts = [
        'tanggal_aktivitas' => 'datetime',
    ];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'id_tiket', 'id_tiket');
    }

    public function agent()
    {
        return $this->belongsTo(CustomerService::class, 'id_agent', 'id_agent');
    }
}
