<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tiket extends Model
{
    use HasFactory;

    protected $table = 'tiket';
    protected $primaryKey = 'id_tiket';

    protected $fillable = [
        'id_nasabah',
        'id_agent',
        'kategori',
        'deskripsi',
        'tanggal_buat',
        'status_tiket',
        'prioritas',
    ];

    protected $casts = [
        'tanggal_buat' => 'datetime',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id_nasabah');
    }

    public function agent()
    {
        return $this->belongsTo(CustomerService::class, 'id_agent', 'id_agent');
    }

    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'id_tiket', 'id_tiket');
    }
}
