<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerService extends Model
{
    use HasFactory;

    protected $table = 'customer_service';
    protected $primaryKey = 'id_agent';

    protected $fillable = [
        'nama_agent',
        'email_agent',
        'divisi',
    ];

    public function tikets()
    {
        return $this->hasMany(Tiket::class, 'id_agent', 'id_agent');
    }

    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'id_agent', 'id_agent');
    }
}
