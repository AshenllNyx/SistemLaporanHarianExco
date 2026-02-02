@extends('layouts.app')

@section('title', 'Semakan Laporan - Admin')

@section('content')

@php
    // --- LOGIC PEMPROSESAN DATA ---
    
    // Decode exco IC array
    $excos = json_decode($laporan->nama_exco, true);
    if (!is_array($excos)) {
        $excos = [$laporan->nama_exco];
    }
    // Filter empty values
    $excos = array_filter($excos);

    // Dapatkan nama EXCO berdasarkan IC (KeyBy IC for easy lookup)
    $senarai_exco = \App\Models\User::whereIn('no_ic', $excos)->get()->keyBy('no_ic');

    // Helper: Tukar IC kepada Nama (untuk Data Tambahan)
    if (!function_exists('tukarKeNama')) {
        function tukarKeNama($value) {
            if (!is_array($value)) {
                $value = explode(',', $value);
            }
            $value = array_filter(array_map('trim', $value));
            
            if (empty($value)) return [];

            $pelajar = \App\Models\Pelajar::whereIn('no_ic', $value)->get()->keyBy('no_ic');

            return array_map(function($v) use ($pelajar) {
                return $pelajar[$v]->nama ?? $v;
            }, $value);
        }
    }

    // Helper: Check if array contains only ICs
    if (!function_exists('semuaIC')) {
        function semuaIC($arr) {
            if (!is_array($arr)) return false;
            foreach ($arr as $item) {
                if (!preg_match('/^[0-9]{6,12}$/', $item)) {
                    return false;
                }
            }
            return true;
        }
    }
@endphp

<style>
    /* --- Styles for Admin Review --- */
    .admin-wrap {
        max-width: 1000px;
        margin: 0 auto;
        padding-bottom: 100px;
    }

    /* Print Styles */
    @media print {
        body * { visibility: hidden; }
        #printableArea, #printableArea * { visibility: visible; }
        #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; margin-bottom: 20px !important; }
        .admin-wrap { width: 100%; margin: 0; padding: 0; }
    }

    .card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        margin-bottom: 24px;
        overflow: hidden;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }

    .card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title span {
        font-size: 20px;
    }

    .card-body {
        padding: 24px;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }

    .info-item label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .info-item div {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }

    /* Details Table */
    .details-table {
        width: 100%;
        border-collapse: collapse;
    }

    .details-table tr:not(:last-child) {
        border-bottom: 1px solid #f1f5f9;
    }

    .dt-label {
        padding: 12px 0;
        width: 180px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        vertical-align: top;
    }

    .dt-value {
        padding: 12px 0;
        font-size: 14px;
        color: #1e293b;
        font-weight: 500;
    }

    .simple-list {
        margin: 0;
        padding-left: 20px;
    }

    .simple-list li {
        margin-bottom: 4px;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-yellow { background: #fef3c7; color: #92400e; }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-red { background: #fee2e2; color: #991b1b; }
    .badge-blue { background: #dbeafe; color: #1e40af; }

    /* Action Bar */
    .action-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 100;
        box-shadow: 0 -10px 25px rgba(0,0,0,0.05);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-box {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        padding: 32px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Button Overrides */
    .btn {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }
    .btn-secondary { background: #f1f5f9; color: #475569; }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-primary { background: #2563eb; color: white; box-shadow: 0 4px 12px rgba(37,99,235,0.2); }
    .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); }
    .btn-warning { background: #f59e0b; color: white; box-shadow: 0 4px 12px rgba(245,158,11,0.2); }
    .btn-warning:hover { background: #d97706; transform: translateY(-1px); }
    .btn-success { background: #10b981; color: white; box-shadow: 0 4px 12px rgba(16,185,129,0.2); }
    .btn-success:hover { background: #059669; transform: translateY(-1px); }
</style>

<div class="admin-wrap">

    <div id="printableArea">
        
        {{-- HEADER SECTION --}}
        <div style="margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1 style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; letter-spacing: -0.02em;">
                        @if($laporan->status_laporan === 'disahkan')
                             📜 Laporan Harian (Disahkan)
                        @else
                             🔍 Semakan Laporan
                        @endif
                    </h1>
                    <p style="color: #64748b; margin: 0; font-size: 15px;">Sila semak butiran laporan harian dengan teliti sebelum pengesahan.</p>
                </div>
                
                {{-- Status Badge --}}
                @php $status = $laporan->status_laporan; @endphp
                @if($status === 'draf')
                    <span class="badge badge-yellow" style="padding: 8px 16px; font-size: 13px;">✏️ Draf</span>
                @elseif($status === 'disahkan')
                    <span class="badge badge-green" style="padding: 8px 16px; font-size: 13px;">✅ Disahkan</span>
                @elseif($status === 'dihantar')
                    <span class="badge badge-blue" style="padding: 8px 16px; font-size: 13px;">📤 Dihantar</span>
                @elseif(in_array($status, ['hantar_semula','tolak','perlu_hantar_semula']))
                    <span class="badge badge-red" style="padding: 8px 16px; font-size: 13px;">⚠️ Perlu Hantar Semula</span>
                @endif
            </div>
        </div>

        {{-- MAKLUMAT ASAS CARD --}}
        <div class="card" style="border-left: 5px solid #2563eb;">
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>📅 Tarikh Laporan</label>
                        <div>{{ \Carbon\Carbon::parse($laporan->tarikh_laporan)->format('d F Y') }}</div>
                        <div style="font-size: 13px; color: #64748b; font-weight: 600; margin-top: 2px;">
                            {{ \Carbon\Carbon::parse($laporan->tarikh_laporan)->translatedFormat('l') }}
                        </div>
                    </div>

                    <div class="info-item">
                        <label>🕒 Masa Dihantar</label>
                        <div>
                            @if($laporan->tarikh_hantar)
                                {{ \Carbon\Carbon::parse($laporan->tarikh_hantar)->format('d/m/Y - h:i A') }}
                            @else
                                <span style="color: #94a3b8; font-weight: 500;">Belum dihantar</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <label>👤 EXCO Bertugas</label>
                        <div>
                            @foreach($excos as $ic)
                                <div style="margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
                                    <span style="background: #eff6ff; color: #2563eb; width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px;">👤</span>
                                    {{ $senarai_exco[$ic]->name ?? $ic }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin: 40px 0 20px 0; display: flex; align-items: center; gap: 12px;">
            <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">Butiran Laporan</h3>
            <div style="flex-grow: 1; height: 1px; background: #e2e8f0;"></div>
        </div>

        {{-- LOOP BUTIRAN --}}
        @foreach($laporan->butiranLaporans as $index => $butiran)
            @php
                // TENTUKAN HEADER & ICON
                $icon = '📄';
                $title = 'Butiran Lain';
                $color = '#2563eb'; 

                switch($butiran->jenis_butiran) {
                    case 'dorm':
                        $icon = '🛏️';
                        $title = 'Laporan Kebersihan Dorm: ' . ($butiran->dorm->nama_dorm ?? 'Umum');
                        $color = '#2563eb';
                        break;
                    case 'disiplin':
                        $icon = '⚖️';
                        $title = 'Laporan Disiplin';
                        $color = '#ef4444'; 
                        break;
                    case 'kerosakan':
                        $icon = '🛠️';
                        $title = 'Laporan Kerosakan';
                        $color = '#f59e0b'; 
                        break;
                    case 'pelajar_sakit':
                        $icon = '🚑';
                        $title = 'Laporan Pelajar Sakit';
                        $color = '#10b981'; 
                        break;
                    case 'dewan_makan':
                        $icon = '🍽️';
                        $title = 'Laporan Dewan Makan';
                        $color = '#8b5cf6'; 
                        break;
                }
            @endphp

            <div class="card" style="border-left: 5px solid {{ $color }};">
                <div class="card-header" style="background: {{ $color }}08;">
                    <h4 class="card-title" style="color: {{ $color }};">
                        <span style="background: {{ $color }}15; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">{{ $icon }}</span> 
                        {{ $title }}
                    </h4>
                    <span style="font-size: 11px; color: {{ $color }}; background: {{ $color }}15; padding: 2px 8px; border-radius: 4px; font-weight: 800; text-transform: uppercase;">Bahagian {{ $index + 1 }}</span>
                </div>
                <div class="card-body">
                    <table class="details-table">
                        
                        {{-- Deskripsi Isu --}}
                        @if($butiran->deskripsi_isu)
                        <tr>
                            <td class="dt-label">Deskripsi / Perkara</td>
                            <td class="dt-value" style="font-weight: 700;">{{ $butiran->deskripsi_isu }}</td>
                        </tr>
                        @endif

                        {{-- Data Tambahan Loop --}}
                        @php
                            $extras = is_array($butiran->data_tambahan)
                                ? $butiran->data_tambahan
                                : json_decode($butiran->data_tambahan, true);
                        @endphp

                        @if($extras)
                            @foreach($extras as $key => $val)
                                @php
                                    // Skip keys that usually match deskripsi_isu
                                    if(in_array($key, ['jenis_kesalahan', 'jenis_kerosakan', 'jenis_sakit', 'jenis_isu'])) continue;

                                    // Formating Logic
                                    if (is_array($val)) {
                                        $val = array_map(function($v){
                                            if (is_array($v) && isset($v['ic'])) return $v['ic'];
                                            if (is_object($v) && isset($v->ic)) return $v->ic;
                                            return $v;
                                        }, $val);
                                    }
                                    if (is_object($val)) $val = (array)$val;
                                    if (is_string($val) && str_contains($val, ',')) $val = array_map('trim', explode(',', $val));

                                    // Special Handling for 'pelajar' array or IC arrays
                                    if ($key === 'pelajar' || (is_array($val) && semuaIC($val))) {
                                        $papar = tukarKeNama($val);
                                    } else {
                                        $papar = $val;
                                    }

                                    $isKebersihan = $key === 'kategori' || $key === 'kategori_kebersihan';
                                @endphp

                                <tr>
                                    <td class="dt-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                    <td class="dt-value">
                                        @if(is_array($papar))
                                            @if(count($papar) > 0)
                                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                                    @foreach($papar as $pItem)
                                                        <span style="background: #f1f5f9; color: #475569; padding: 2px 10px; border-radius: 6px; font-size: 13px; border: 1px solid #e2e8f0;">{{ $pItem }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span style="color: #94a3b8; font-style: italic;">Tiada maklumat</span>
                                            @endif
                                        @else
                                            @if($isKebersihan)
                                                <span class="badge" style="background: {{ $papar == 'Bersih' ? '#dcfce7' : ($papar == 'Sederhana' ? '#fef3c7' : '#fee2e2') }}; color: {{ $papar == 'Bersih' ? '#166534' : ($papar == 'Sederhana' ? '#92400e' : '#991b1b') }};">
                                                    {{ $papar == 'Bersih' ? '✨ '.$papar : ($papar == 'Sederhana' ? '👌 '.$papar : '⚠️ '.$papar) }}
                                                </span>
                                            @else
                                                {!! nl2br(e($papar ?: '-')) !!}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        @endforeach

        @if($laporan->butiranLaporans->isEmpty())
            <div class="card">
                <div class="card-body" style="text-align: center; color: #94a3b8; padding: 60px 20px;">
                    <div style="font-size: 48px; margin-bottom: 16px;">📂</div>
                    <p style="font-weight: 600;">Tiada butiran direkodkan dalam laporan ini.</p>
                </div>
            </div>
        @endif

    </div> {{-- End printableArea --}}


    {{-- ACTION BAR --}}
    <div class="action-bar no-print">
        <a href="{{ route('semakan.Laporan') }}" class="btn btn-secondary">
            ⬅ Kembali Ke Senarai
        </a>

        <div style="display: flex; gap: 12px;">
            @if($laporan->status_laporan === 'disahkan')
                <button onclick="window.print()" class="btn btn-success">
                    🖨️ Cetak Laporan Rasmi
                </button>
            @else
                <button type="button" class="btn btn-warning btn-resubmit">
                    ⚠️ Hantar Semula
                </button>

                <form action="{{ route('laporan.pengesahan', $laporan->id_laporan) }}" method="POST" onsubmit="return confirm('Sahkan laporan ini secara rasmi?');" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        ✅ Sahkan Laporan
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>

{{-- MODAL HANTAR SEMULA --}}
<div id="resubmitModal" class="modal-overlay">
    <div class="modal-box">
        <h3 style="margin-top:0; font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;">⚠️ Hantar Semula Laporan</h3>
        <p style="color:#64748b; margin-bottom: 24px; font-size: 14px; line-height: 1.5;">Laporan akan dikembalikan kepada pelapor untuk dibuat pembetulan. Sila nyatakan sebab di bawah.</p>

        <form id="resubmitForm" action="{{ route('laporan.hantarSemula', $laporan->id_laporan) }}" method="POST">
            @csrf
            <div style="margin-bottom:24px;">
                <label for="sebab_hantar_semula" style="display:block;font-weight:700;margin-bottom:8px; font-size:12px; color: #475569; text-transform: uppercase;">Maklum Balas / Sebab (Pilihan)</label>
                <textarea name="sebab_hantar_semula" id="sebab_hantar_semula" rows="4" 
                    placeholder="Contoh: Maklumat kehadiran pelajar di Blok A tidak lengkap. Sila semak semula." 
                    style="width:100%; padding:14px; border:1px solid #e2e8f0; border-radius:12px; font-family:inherit; font-size:14px; resize:none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#2563eb'"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" id="resubmitCancel" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-warning">Sahkan Hantar Semula</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('resubmitModal');
        const openBtn = document.querySelector('.btn-resubmit');
        const cancelBtn = document.getElementById('resubmitCancel');

        if (openBtn) {
            openBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
                document.getElementById('sebab_hantar_semula').focus();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') modal.style.display = 'none';
        });
    });
</script>

@endsection
