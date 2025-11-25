<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporans';
    protected $primaryKey = 'id_laporan';
    public $incrementing = true;
    protected $fillable = [
        'no_ic','nama_exco','tarikh_laporan','tarikh_hantar','status_laporan'
    ];

    public function butiran()
    {
        return $this->hasMany(ButiranLaporan::class, 'id_laporan', 'id_laporan');
    }
}
