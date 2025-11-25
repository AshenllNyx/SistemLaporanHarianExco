<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Dorm;
use App\Models\LaporanHarian;
use App\Models\ButiranLaporan;

class LaporanController extends Controller
{
    // Show dorm form (step 1)
    public function create()
    {
        $dorms = Dorm::all();
        return view('laporan.create', compact('dorms'));
    }

    // Store dorm step (create laporan draf + butiran_laporans)
    public function storeDorm(Request $request)
    {
        $request->validate([
            'kategori' => 'required|array',
            // absent may be optional
        ]);

        // create laporan draf
        $laporan = LaporanHarian::create([
            'no_ic' => Auth::user()->no_ic ?? Auth::id(),
            'nama_exco' => Auth::user()->name ?? 'EXCO',
            'tarikh_laporan' => Carbon::now()->toDateString(),
            'status_laporan' => 'draf',
        ]);

        $kategori = $request->input('kategori', []);
        $absent = $request->input('absent', []); // array of arrays

        foreach ($kategori as $dorm_id => $kat_value) {
            $data_tambahan = [
                'kategori_kebersihan' => $kat_value,
                'tidak_hadir' => $absent[$dorm_id] ?? [],
            ];

            ButiranLaporan::create([
                'id_laporan' => $laporan->id_laporan,
                'id_dorm' => $dorm_id,
                'jenis_butiran' => 'dorm',
                'deskripsi_isu' => null,
                'data_tambahan' => $data_tambahan,
            ]);
        }

        // redirect to step2 (soalan disiplin)
        return redirect()->route('laporan.disiplin.soalan', $laporan->id_laporan)
                         ->with('success','Draf laporan disimpan. Seterusnya pilih laporan disiplin.');
    }

    // Step2: soalan yes/no untuk laporan disiplin
    public function soalanDisiplin($id)
    {
        $laporan = LaporanHarian::findOrFail($id);
        return view('laporan.step2', compact('laporan'));
    }

    // Show create form for laporan disiplin
    public function createDisiplin($id)
    {
        $laporan = LaporanHarian::with('butiranLaporans')->findOrFail($id);

        // For convenience, build a flattened student list per dorm for selection
        $students = [];
        foreach ($laporan->butiranLaporans as $b) {
            $dorm = $b->dorm;
            if ($dorm && is_array($dorm->senarai_pelajar)) {
                foreach ($dorm->senarai_pelajar as $p) {
                    // ensure unique by no_ic
                    $students[$p['no_ic']] = $p;
                }
            }
        }

        return view('laporan.disiplin_create', compact('laporan','students'));
    }

    // Store disiplin records as ButiranLaporan entries with jenis_butiran = 'disiplin'
    public function storeDisiplin(Request $request)
    {
        $request->validate([
            'id_laporan' => 'required|exists:laporans,id_laporan',
            'pelajar' => 'required|array',
            'jenis_kesalahan' => 'required|string|max:255',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $id_laporan = $request->input('id_laporan');

        // store a ButiranLaporan record for discipline (we store pelajar list in data_tambahan)
        $data_tambahan = [
            'pelajar' => $request->input('pelajar'),
            'jenis_kesalahan' => $request->input('jenis_kesalahan'),
            'tindakan' => $request->input('tindakan'),
            'catatan' => $request->input('catatan'),
        ];

        ButiranLaporan::create([
            'id_laporan' => $id_laporan,
            'id_dorm' => null,
            'jenis_butiran' => 'disiplin',
            'deskripsi_isu' => $request->input('jenis_kesalahan'),
            'data_tambahan' => $data_tambahan,
        ]);

        return redirect()->route('laporan.review', ['laporan' => $id_laporan])
                         ->with('success', 'Laporan disiplin disimpan.');
    }

    // Review (summary)
    public function review($laporanId)
    {
        $laporan = LaporanHarian::with('butiranLaporans.dorm')->findOrFail($laporanId);
        return view('laporan.review', compact('laporan'));
    }

    // Final submit
    public function submit(Request $request, $laporanId)
    {
        $laporan = LaporanHarian::findOrFail($laporanId);
        $laporan->status_laporan = 'dihantar';
        $laporan->tarikh_hantar = Carbon::now()->toDateString();
        $laporan->save();

        return redirect()->route('homepage')->with('success','Laporan berjaya dihantar.');
    }
}
