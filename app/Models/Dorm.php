<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;  

class Dorm extends Model
{
    use HasFactory;
    protected $table = 'dorms';
    protected $primaryKey = 'id_dorm';
    protected $fillable = [
        'nama_dorm',
        'senarai_pelajar',
        'capacity',
    ];

    protected $casts = [
        'senarai_pelajar' => 'array',
        'capacity' => 'integer',
    ];

    public function getSenaraiPelajarAttribute($value)
    {
        // if already array (cast may handle), return it
        if (is_array($value)) {
            return $value;
        }

        // try decoding once
        $decoded = json_decode($value, true);

        // if decoding gives a string, decode again (double encoded)
        if (is_string($decoded)) {
            $decoded2 = json_decode($decoded, true);
            return is_array($decoded2) ? $decoded2 : [];
        }

        return is_array($decoded) ? $decoded : [];
    }
}
