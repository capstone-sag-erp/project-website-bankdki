<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabah';
    protected $primaryKey = 'id_nasabah';

    protected $fillable = [
        'nama',
        'no_ktp',
        'alamat',
        'email',
        'no_telepon',
        'tanggal_daftar',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
    ];

    public function crmLeads()
    {
        return $this->hasMany(CrmLead::class, 'id_nasabah', 'id_nasabah');
    }

    public function tikets()
    {
        return $this->hasMany(Tiket::class, 'id_nasabah', 'id_nasabah');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'nasabah_id', 'id_nasabah');
    }
}
