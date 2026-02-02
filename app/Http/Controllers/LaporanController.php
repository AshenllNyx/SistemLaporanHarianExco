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
        $laporans = LaporanHarian::with('butiranLaporans.dorm')
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

        // 2. KIRA BILANGAN PELAJAR & KEHADIRAN (Ikut laporan terbaru setiap dorm)
        $totalStudents = \App\Models\Pelajar::count();
        $allDorms = \App\Models\Dorm::all();
        $absentIcs = [];

        foreach ($allDorms as $dorm) {
            // Cari butiran laporan 'dorm' yang terbaru bagi dorm ini (Dihantar/Disahkan)
            $latestButiran = \App\Models\ButiranLaporan::where('id_dorm', $dorm->id_dorm)
                ->where('jenis_butiran', 'dorm')
                ->whereHas('laporan', function($q) {
                    $q->whereIn('status_laporan', ['dihantar', 'disahkan']);
                })
                ->orderBy('id_butiran_laporan', 'desc') // more reliable than latest() sometimes
                ->first();

            if ($latestButiran) {
                $data = is_array($latestButiran->data_tambahan) ? $latestButiran->data_tambahan : json_decode($latestButiran->data_tambahan, true);
                $absents = $data['tidak_hadir'] ?? [];
                if (is_array($absents)) {
                    $absentIcs = array_merge($absentIcs, $absents);
                }
            }
        }
        
        $uniqueAbsentCount = count(array_unique($absentIcs));
        $todayPresent = $totalStudents - $uniqueAbsentCount;

        return view('homepage', compact('laporans', 'laporanHantarSemula', 'totalStudents', 'todayPresent'));
    }

    // Admin homepage - report tracking & statistics
   public function homepageAdmin(Request $request)
{
    // 1. STATISTIK GLOBAL (Tidak terkesan dengan filter)
    // Kita buat kiraan terus daripada model tanpa filter daripada $request
    $totalLaporans = LaporanHarian::count();
    $draftCount = LaporanHarian::where('status_laporan', 'draf')->count();
    $submittedCount = LaporanHarian::where('status_laporan', 'dihantar')->count();
    $resubmitCount = LaporanHarian::whereIn('status_laporan', ['hantar_semula', 'tolak', 'perlu_hantar_semula'])->count();

    // 2. KIRA BILANGAN PELAJAR & KEHADIRAN (Ikut laporan terbaru setiap dorm)
    $totalStudents = \App\Models\Pelajar::count();
    $allDorms = \App\Models\Dorm::all();
    $absentIcs = [];

    foreach ($allDorms as $dorm) {
        $latestButiran = \App\Models\ButiranLaporan::where('id_dorm', $dorm->id_dorm)
            ->where('jenis_butiran', 'dorm')
            ->whereHas('laporan', function($q) {
                $q->whereIn('status_laporan', ['dihantar', 'disahkan']);
            })
            ->orderBy('id_butiran_laporan', 'desc')
            ->first();

        if ($latestButiran) {
            $data = is_array($latestButiran->data_tambahan) ? $latestButiran->data_tambahan : json_decode($latestButiran->data_tambahan, true);
            $absents = $data['tidak_hadir'] ?? [];
            if (is_array($absents)) {
                $absentIcs = array_merge($absentIcs, $absents);
            }
        }
    }
    
    $uniqueAbsentCount = count(array_unique($absentIcs));
    $todayPresent = $totalStudents - $uniqueAbsentCount;

    return view('homepageAdmin', compact(
        'totalLaporans',
        'draftCount', 
        'submittedCount',
        'resubmitCount',
        'totalStudents',
        'todayPresent'
    ));
}

public function semakanLaporan(Request $request)
{


    // 2. QUERY UNTUK SENARAI REKOD (Terkesan dengan filter)
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');
    $status = $request->get('status', 'all');

    $query = LaporanHarian::with('butiranLaporans.dorm');

    // Filter Status
    if ($status && $status !== 'all') {
        if ($status === 'resubmit') {
            $query->whereIn('status_laporan', ['hantar_semula', 'tolak', 'perlu_hantar_semula']);
        } else {
            $query->where('status_laporan', $status);
        }
    }

    // Filter Tarikh
    if ($fromDate) {
        $query->whereDate('tarikh_laporan', '>=', $fromDate);
    }
    if ($toDate) {
        $query->whereDate('tarikh_laporan', '<=', $toDate);
    }

    // Ambil data yang telah difilter untuk jadual sahaja
    $recentLaporans = $query->orderBy('tarikh_laporan', 'desc')->take(50)->get();

    // 3. PEMPROSESAN NAMA EXCO (Hanya untuk data dalam jadual)
    $parseExcoNames = function($nama_exco_json) {
        $excoIcs = json_decode($nama_exco_json, true) ?? [];
        $excoIcs = array_filter($excoIcs, fn($ic) => !is_null($ic) && $ic !== '');
        
        if (empty($excoIcs)) return '-';
        
        $names = User::whereIn('no_ic', $excoIcs)->pluck('name')->toArray();
        return !empty($names) ? implode(', ', $names) : '-';
    };

    foreach ($recentLaporans as $laporan) {
        $laporan->display_nama_exco = $parseExcoNames($laporan->nama_exco);
    }

    

    return view('semakanLaporan', compact(
        'recentLaporans'
        
    ));
}


    // Show dorm form (step 1)
    public function create()
    {
        $dorms = Dorm::with('pelajars')->orderBy('nama_dorm', 'asc')->get();

        $senaraiExco = User::where('level', 'user')->get();

        return view('laporan.create', compact('dorms', 'senaraiExco'));
    }

    // Edit dorm form for draft or resubmit reports
    public function editDorm($id)
    {
        $laporan = LaporanHarian::with('butiranLaporans')->findOrFail($id);

        // Only allow editing if in draft or needs resubmission
        if (!in_array($laporan->status_laporan, ['draf', 'dihantar', 'hantar_semula', 'tolak', 'perlu_hantar_semula'])) {
            return redirect()->back()->with('error', 'Laporan ini tidak boleh diubah.');
        }

        $dorms = Dorm::with('pelajars')->orderBy('nama_dorm', 'asc')->get();
        $senaraiExco = User::where('level', 'user')->get();

        // Get existing data
        $butiranByDorm = [];
        foreach ($laporan->butiranLaporans as $butiran) {
            if ($butiran->jenis_butiran === 'dorm') {
                $butiranByDorm[$butiran->id_dorm] = $butiran;
            }
        }

        // Decode exco names
        $excos = json_decode($laporan->nama_exco, true) ?? [];

        // Detect existing sections
        $existingSections = $laporan->butiranLaporans->pluck('jenis_butiran')->unique()->toArray();
        
        // Map sections for the UI dashboard
        $sectionStatus = [
            'disiplin' => in_array('disiplin', $existingSections),
            'kerosakan' => in_array('kerosakan', $existingSections),
            'pelajar_sakit' => in_array('pelajar_sakit', $existingSections),
            'dewan_makan' => in_array('dewan_makan', $existingSections),
        ];

        return view('laporan.edit', compact('laporan', 'dorms', 'senaraiExco', 'butiranByDorm', 'excos', 'sectionStatus'));
    }

    // Update dorm data for existing laporan
    public function updateDorm(Request $request, $id)
    {
        $laporan = LaporanHarian::with('butiranLaporans')->findOrFail($id);

        // Only allow updating if in draft or needs resubmission
        if (!in_array($laporan->status_laporan, ['draf', 'dihantar', 'hantar_semula', 'tolak', 'perlu_hantar_semula'])) {
            return redirect()->back()->with('error', 'Laporan ini tidak boleh diubah.');
        }

        $request->validate([
            'kategori' => 'required|array',
        ]);

        // Update exco names if changed
        $laporan->nama_exco = json_encode([
            $request->exco1,
            $request->exco2,
        ]);
        $laporan->save();

        // Delete existing dorm butiran records
        $laporan->butiranLaporans()->where('jenis_butiran', 'dorm')->delete();

        // Create new butiran records with updated data
        $kategori = $request->input('kategori', []);
        $absent = $request->input('absent', []);

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

        return redirect()->route('laporan.disiplin.soalan', $laporan->id_laporan)
                         ->with('success', 'Data laporan dorm telah dikemaskini.');
    }

    /**
     * Destroy the specified laporan and its related butiran.
     */
    public function destroy($id)
    {
        $laporan = LaporanHarian::with('butiranLaporans')->find($id);
        if (! $laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemui.');
        }

        // Authorization check: Only admin OR owner (if still draft/resubmit)
        $user = Auth::user();
        $isOwner = $user && $laporan->no_ic == $user->no_ic;
        $isAdmin = $user && $user->level === 'admin';
        
        if (!$isAdmin) {
            if (!$isOwner) {
                return redirect()->back()->with('error', 'Anda tiada kebenaran untuk memadam laporan ini.');
            }
            if (!in_array($laporan->status_laporan, ['draf', 'hantar_semula', 'tolak', 'perlu_hantar_semula'])) {
                return redirect()->back()->with('error', 'Laporan yang telah dihantar/disahkan tidak boleh dipadam oleh user.');
            }
        }

        // delete detail records first
        if ($laporan->butiranLaporans()->exists()) {
            $laporan->butiranLaporans()->delete();
        }

        $laporan->delete();

        $redirect = $isAdmin ? route('homepage.admin') : route('homepage');
        return redirect($redirect)->with('success', 'Laporan telah dipadam.');
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
        $laporan = LaporanHarian::with('butiranLaporans.dorm.pelajars')->findOrFail($id);

        // Senarai pelajar dari dorm yang dipilih dalam laporan (dari jadual pelajars)
        $students = [];
        foreach ($laporan->butiranLaporans as $b) {
            $dorm = $b->dorm;
            if (!$dorm) {
                continue;
            }
            foreach ($dorm->pelajars ?? [] as $p) {
                $students[$p->no_ic] = ['nama' => $p->nama, 'no_ic' => $p->no_ic];
            }
        }

        // Fallback: jika tiada pelajar dalam pelajars, cuba dari senarai_pelajar (JSON lama)
        if (empty($students)) {
            foreach ($laporan->butiranLaporans as $b) {
                $dorm = $b->dorm;
                if ($dorm && is_array($dorm->senarai_pelajar ?? null)) {
                    foreach ($dorm->senarai_pelajar as $p) {
                        $ic = $p['no_ic'] ?? $p['ic'] ?? null;
                        if ($ic) {
                            $students[$ic] = [
                                'nama' => $p['nama'] ?? $p['name'] ?? '-',
                                'no_ic' => $ic,
                            ];
                        }
                    }
                }
            }
        }

        return view('laporan.disiplin_create', compact('laporan','students'))->with('from', request('from'));
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

        // Redirection logic
        if ($request->input('from') === 'hub') {
            return redirect()->route('laporan.edit', $id_laporan)->with('success', 'Laporan disiplin disimpan.');
        }

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
        asort($dorms); // Sort dorm names alphabetically (A1, A2, etc.)

        return view('laporan.kerosakan_create', compact('laporan', 'dorms'))->with('from', request('from'));
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
        
        if ($request->input('from') === 'hub') {
            return redirect()->route('laporan.edit', $id_laporan)->with('success', 'Laporan kerosakan disimpan.');
        }

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
        $laporan = LaporanHarian::with('butiranLaporans.dorm.pelajars')->findOrFail($id);

        // Senarai pelajar dari dorm yang dipilih dalam laporan (dari jadual pelajars)
        $students = [];
        foreach ($laporan->butiranLaporans as $b) {
            $dorm = $b->dorm;
            if (!$dorm) {
                continue;
            }
            foreach ($dorm->pelajars ?? [] as $p) {
                $students[$p->no_ic] = ['nama' => $p->nama, 'no_ic' => $p->no_ic];
            }
        }

        // Fallback: jika tiada pelajar dalam pelajars, cuba dari senarai_pelajar (JSON lama)
        if (empty($students)) {
            foreach ($laporan->butiranLaporans as $b) {
                $dorm = $b->dorm;
                if ($dorm && is_array($dorm->senarai_pelajar ?? null)) {
                    foreach ($dorm->senarai_pelajar as $p) {
                        $p_array = (array)$p; 
                        $ic = $p_array['no_ic'] ?? $p_array['ic'] ?? null;
                        if ($ic) {
                            $students[$ic] = [
                                'nama' => $p_array['nama'] ?? $p_array['name'] ?? '-',
                                'no_ic' => $ic,
                            ];
                        }
                    }
                }
            }
        }

        return view('laporan.pelajar_sakit_create', compact('laporan', 'students'))->with('from', request('from'));
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

        if ($request->input('from') === 'hub') {
            return redirect()->route('laporan.edit', $id_laporan)->with('success', 'Laporan pelajar sakit disimpan.');
        }

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
        return view('laporan.dewan_makan_create', compact('laporan'))->with('from', request('from'));
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
        if ($request->input('from') === 'hub') {
            return redirect()->route('laporan.edit', $id_laporan)->with('success', 'Laporan dewan makan disimpan.');
        }

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
        $laporan->tarikh_hantar = Carbon::now();
        $laporan->save();

        return redirect()->route('homepage')->with('success','Laporan berjaya dihantar.');
    }

    //review admin
    public function reviewAdmin($laporanId)
    {
        $laporan = LaporanHarian::with('butiranLaporans.dorm')->findOrFail($laporanId);
        // Decode EXCO IC list
        $excos = json_decode($laporan->nama_exco, true) ?? [];
        // Fetch EXCO names
        $senarai_exco = User::whereIn('no_ic', $excos)->get()->keyBy('no_ic');
        return view('laporan.reviewAdmin', compact('laporan', 'senarai_exco'));
    }

    /**
     * Pengesahan oleh admin — mark laporan as confirmed.
     */
    public function pengesahan(Request $request, $laporanId)
    {
        $laporan = LaporanHarian::findOrFail($laporanId);
        $laporan->status_laporan = 'disahkan';
        $laporan->save();

        return redirect()->route('homepage.admin')->with('success', 'Laporan telah disahkan.');
    }

    public function hantarSemula(Request $request, $laporanId)
    {
        $request->validate([
            'sebab_hantar_semula' => 'nullable|string|max:2000',
        ]);

        $laporan = LaporanHarian::findOrFail($laporanId);
        $laporan->status_laporan = 'hantar_semula';
        $laporan->sebab_hantar_semula = $request->input('sebab_hantar_semula');
        $laporan->save();

        return redirect()->route('homepage.admin')->with('success', 'Laporan telah ditandakan untuk dihantar semula.');
    }

    /**
     * Halaman Senarai Kehadiran
     */
    public function kehadiranIndex(Request $request)
    {
        // 1. Dapatkan laporan yang ada butiran 'dorm'
        $query = LaporanHarian::with(['butiranLaporans' => function($q){
            $q->where('jenis_butiran', 'dorm')->with('dorm.pelajars');
        }]);

        // Filter Tarikh (Optional)
        if ($request->has('from_date') && $request->get('from_date') != '') {
            $query->whereDate('tarikh_laporan', '>=', $request->get('from_date'));
        }
        if ($request->has('to_date') && $request->get('to_date') != '') {
            $query->whereDate('tarikh_laporan', '<=', $request->get('to_date'));
        }

        $laporans = $query->orderBy('tarikh_laporan', 'desc')->get();

        // 2. Process data untuk view
        $attendanceData = [];

        foreach ($laporans as $laporan) {
            
            // Nama Exco
            $excoIcs = json_decode($laporan->nama_exco, true) ?? [];
            if (!is_array($excoIcs)) $excoIcs = [$laporan->nama_exco];
            $excoIcs = array_filter($excoIcs);
            $namaExco = '-';
            if (!empty($excoIcs)) {
                $names = User::whereIn('no_ic', $excoIcs)->pluck('name')->toArray();
                $namaExco = implode(', ', $names);
            }

            // Aggregation Variables
            $reportTotalStudents = 0;
            $reportPresentCount = 0;
            $reportAbsentCount = 0;
            $reportAbsentList = [];
            $dormNames = [];

            foreach ($laporan->butiranLaporans as $butiran) {
                if ($butiran->jenis_butiran !== 'dorm' || !$butiran->dorm) continue;

                $dormName = $butiran->dorm->nama_dorm;
                $dormNames[] = $dormName;
                
                $totalStudents = $butiran->dorm->pelajars->count();
                
                // Get Absent Data
                $dataTambahan = is_array($butiran->data_tambahan) ? $butiran->data_tambahan : json_decode($butiran->data_tambahan, true);
                $absentIcs = $dataTambahan['tidak_hadir'] ?? [];
                if (!is_array($absentIcs)) $absentIcs = [];

                $absentCount = count($absentIcs);
                $presentCount = $totalStudents - $absentCount;

                // Aggregate
                $reportTotalStudents += $totalStudents;
                $reportPresentCount += $presentCount;
                $reportAbsentCount += $absentCount;

                // Absent Names with Dorm
                if ($absentCount > 0) {
                    // Fetch students keyed by IC for O(1) lookup
                    $studentsDB = \App\Models\Pelajar::whereIn('no_ic', $absentIcs)->get()->keyBy('no_ic');
                    
                    foreach ($absentIcs as $ic) {
                        $studentName = $studentsDB[$ic]->nama ?? $ic; // Fallback to IC/Name if not found
                        
                        $reportAbsentList[] = [
                            'name' => $studentName,
                            'dorm' => $dormName
                        ];
                    }
                }
            }
            
            // Only add if there are dorm details
            if (!empty($dormNames)) {
                 $attendanceData[] = (object) [
                    'id_laporan' => $laporan->id_laporan,
                    'tarikh' => $laporan->tarikh_laporan,
                    'nama_exco' => $namaExco,
                    'dorms' => implode(', ', array_unique($dormNames)),
                    'total' => $reportTotalStudents,
                    'present' => $reportPresentCount,
                    'absent_count' => $reportAbsentCount,
                    'absent_list' => $reportAbsentList, // Array of objects {name, dorm}
                    'status' => $laporan->status_laporan
                ];
            }
        }

        return view('kehadiran.index', compact('attendanceData'));
    }
}