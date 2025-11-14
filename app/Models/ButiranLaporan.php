<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;  

class ButiranLaporan extends Model
{
    use HasFactory;
    protected $table = 'butiran_laporans';
    protected $primaryKey = 'id_butiran_laporan';
    protected $fillable = [
        'id_laporan',
        'id_dorm',
        'jenis_butiran',
        'deskripsi_isu',
        'data_tambahan',
    ];

    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public function laporanHarian()
    {
        return $this->belongsTo(LaporanHarian::class, 'id_laporan', 'id_laporan');
    }   

    public function dorm()
    {
        return $this->belongsTo(Dorm::class, 'id_dorm', 'id_dorm');
    }   

}
