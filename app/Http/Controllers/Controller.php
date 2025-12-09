<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanHarian;

class LoginController extends Controller
{
    public function homepage()
    {
        $user = Auth::user();

        // Senarai laporan user ini sahaja
        $laporans = LaporanHarian::with('butiranLaporans')
                    ->where('no_ic', $user->no_ic)
                    ->orderBy('tarikh_laporan', 'desc')
                    ->get();

        // Jika nak letak fungsi “hantar semula”
        $laporanHantarSemula = LaporanHarian::where('no_ic', $user->no_ic)
                    ->where('status_laporan', 'perlu_hantar_semula')
                    ->get();

        return view('homepage', compact('laporans', 'laporanHantarSemula'));
    }
}
