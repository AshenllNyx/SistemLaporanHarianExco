<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelajar extends Model
{
     use HasFactory;

    protected $fillable = [
        'nama',
        'no_ic',
        'jantina',
        'dorm_id',
    ];

    public function dorm()
    {
        return $this->belongsTo(Dorm::class, 'dorm_id', 'id_dorm');
    }
}
