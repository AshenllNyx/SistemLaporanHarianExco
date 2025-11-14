<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Factories\HasFactory;  



class LaporanHarian extends Model
{
    use HasFactory;
    
    protected $table = 'laporans';
    protected $primaryKey = 'id_laporan';
    protected $fillable = [
        'no_ic',
        'nama_exco',
        'tarikh_laporan',
        'tarikh_hantar',
        'sebab_hantar_semula',
        'status_laporan',
    ];

    public function butiranLaporans()
    {
        return $this->hasMany(ButiranLaporan::class, 'id_laporan', 'id_laporan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'no_ic', 'no_ic');
    }   
}
