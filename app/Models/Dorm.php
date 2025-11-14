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
    ];

    protected $casts = [
        'senarai_pelajar' => 'array',
    ];
}
