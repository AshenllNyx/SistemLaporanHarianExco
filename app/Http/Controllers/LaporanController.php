<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Dorm;
use App\Models\LaporanHarian;
use App\Models\ButiranLaporan;
use App\Models\User;

class LaporanController extends Controller
{
    public function homepage()
    {
        $user = Auth::user();

        // semua laporan milik user (ubah filter jika mahu semua user)
        $laporans = LaporanHarian::with('butiranLaporans')
                    ->when($user, function($q) use ($user) {
                        // filter supaya user hanya nampak laporan dia sendiri
                        $q->where('no_ic', $user->no_ic);
                    })
                    ->orderBy('tarikh_laporan', 'desc')
                    ->get();

        // laporan yang perlu dihantar semula (ubah status value ikut DB anda)
        $laporanHantarSemula = $laporans->filter(function($l) {
            return in_array($l->status_laporan, ['hantar_semula','tolak','perlu_hantar_semula']);
        });

        return view('homepage', compact('laporans', 'laporanHantarSemula'));
    }

    // Show dorm form (step 1)
    public function create()
    {
        $dorms = Dorm::all();

        $senaraiExco = User::where('level', 'user')->get();

        return view('laporan.create', compact('dorms', 'senaraiExco'));
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
            'nama_exco' => json_encode([
                $request->exco1,
                $request->exco2,
            ]),
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

    // ========================================
    // DISIPLIN
    // ========================================

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

        // ❌ LAMA: redirect ke review
        // return redirect()->route('laporan.review', ['laporan' => $id_laporan])

        // ✅ BARU: redirect ke soalan kerosakan
        return redirect()->route('laporan.kerosakan.soalan', $id_laporan)
                         ->with('success', 'Laporan disiplin disimpan.');
    }

    // ========================================
    // KEROSAKAN
    // ========================================

    // Soalan Kerosakan? (YA / TIDAK)
    public function soalanKerosakan($id)
    {
        $laporan = LaporanHarian::findOrFail($id);
        return view('laporan.soalan_kerosakan', compact('laporan'));
    }

    // Show create form for laporan kerosakan
    public function createKerosakan($id)
    {
        $laporan = LaporanHarian::with('butiranLaporans')->findOrFail($id);

        // Get all dorms from this laporan for selection
        $dorms = [];
        foreach ($laporan->butiranLaporans as $b) {
            if ($b->dorm) {
                $dorms[$b->dorm->id_dorm] = $b->dorm->nama_dorm;
            }
        }

        return view('laporan.kerosakan_create', compact('laporan', 'dorms'));
    }

    // Store kerosakan
    public function storeKerosakan(Request $request)
    {
        $request->validate([
            'id_laporan' => 'required|exists:laporans,id_laporan',
            'id_dorm' => 'required|exists:dorms,id_dorm',
            'jenis_kerosakan' => 'required|string|max:255',
            'lokasi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $id_laporan = $request->input('id_laporan');

        $data_tambahan = [
            'jenis_kerosakan' => $request->input('jenis_kerosakan'),
            'lokasi' => $request->input('lokasi'),
            'catatan' => $request->input('catatan'),
        ];

        ButiranLaporan::create([
            'id_laporan' => $id_laporan,
            'id_dorm' => $request->input('id_dorm'),
            'jenis_butiran' => 'kerosakan',
            'deskripsi_isu' => $request->input('jenis_kerosakan'),
            'data_tambahan' => $data_tambahan,
        ]);
        
        // ✅ BARU: redirect ke soalan pelajar sakit
        return redirect()->route('laporan.pelajarsakit.soalan', $id_laporan)
                         ->with('success', 'Laporan kerosakan disimpan.');
    }

    // ========================================
    // PELAJAR SAKIT
    // ========================================

    // Soalan Pelajar Sakit? (YA / TIDAK)
    public function soalanPelajarSakit($id)
    {
        $laporan = LaporanHarian::findOrFail($id);
        return view('laporan.soalan_pelajar_sakit', compact('laporan'));
    }

    // Show create form for laporan pelajar sakit
    public function createPelajarSakit($id)
    {
        $laporan = LaporanHarian::with('butiranLaporans')->findOrFail($id);

        // Get all students from dorms
        $students = [];
        foreach ($laporan->butiranLaporans as $b) {
            $dorm = $b->dorm;
            if ($dorm && is_array($dorm->senarai_pelajar)) {
                foreach ($dorm->senarai_pelajar as $p) {
                    $students[$p['no_ic']] = $p;
                }
            }
        }

        return view('laporan.pelajar_sakit_create', compact('laporan', 'students'));
    }

    // Store pelajar sakit
    // Store pelajar sakit
    public function storePelajarSakit(Request $request)
    {
        $request->validate([
            'id_laporan' => 'required|exists:laporans,id_laporan',
            'pelajar' => 'required|array',
            'jenis_sakit' => 'required|string|max:255',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $id_laporan = $request->input('id_laporan');

        $data_tambahan = [
            'pelajar' => $request->input('pelajar'),
            'jenis_sakit' => $request->input('jenis_sakit'),
            'tindakan' => $request->input('tindakan'),
            'catatan' => $request->input('catatan'),
        ];

        ButiranLaporan::create([
            'id_laporan' => $id_laporan,
            'id_dorm' => null,
            'jenis_butiran' => 'pelajar_sakit',
            'deskripsi_isu' => $request->input('jenis_sakit'),
            'data_tambahan' => $data_tambahan,
        ]);

        // ✅ Redirect ke soalan dewan makan
        return redirect()->route('laporan.dewanmakan.soalan', $id_laporan)
                         ->with('success', 'Laporan pelajar sakit disimpan.');
    }

    // ========================================
    // DEWAN MAKAN
    // ========================================

    // Soalan Dewan Makan? (YA / TIDAK)
    public function soalanDewanMakan($id)
    {
        $laporan = LaporanHarian::findOrFail($id);
        return view('laporan.soalan_dewan_makan', compact('laporan'));
    }

    // Show create form for laporan dewan makan
    public function createDewanMakan($id)
    {
        $laporan = LaporanHarian::findOrFail($id);
        return view('laporan.dewan_makan_create', compact('laporan'));
    }

    // Store dewan makan
    public function storeDewanMakan(Request $request)
    {
        $request->validate([
            'id_laporan' => 'required|exists:laporans,id_laporan',
            'jenis_isu' => 'required|string|max:255',
            'masa_makan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $id_laporan = $request->input('id_laporan');

        $data_tambahan = [
            'jenis_isu' => $request->input('jenis_isu'),
            'masa_makan' => $request->input('masa_makan'),
            'catatan' => $request->input('catatan'),
        ];

        ButiranLaporan::create([
            'id_laporan' => $id_laporan,
            'id_dorm' => null,
            'jenis_butiran' => 'dewan_makan',
            'deskripsi_isu' => $request->input('jenis_isu'),
            'data_tambahan' => $data_tambahan,
        ]);

        // Next: review (final step sebelum submit)
        return redirect()->route('laporan.review', $id_laporan)
                         ->with('success', 'Laporan dewan makan disimpan.');
    }

    // ========================================
    // REVIEW & SUBMIT
    // ========================================

    // Review (summary)
    public function review($laporanId)
    {
        $laporan = LaporanHarian::with('butiranLaporans.dorm')->findOrFail($laporanId);

        // Decode EXCO IC list
        $excos = json_decode($laporan->nama_exco, true) ?? [];

        // Fetch EXCO names
        $senarai_exco = User::whereIn('no_ic', $excos)->get()->keyBy('no_ic');

        return view('laporan.review', compact('laporan', 'senarai_exco'));
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