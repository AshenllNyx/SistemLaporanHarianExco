@extends('layouts.app')

@section('title', 'Sunting Laporan Harian')

@section('content')
<style>
    .hub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .hub-card {
        background: white;
        padding: 24px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        border: 1px solid #f1f5f9;
        transition: transform 0.2s ease;
    }

    .hub-card:hover {
        transform: translateY(-4px);
    }

    .hub-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 16px;
    }

    .hub-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .hub-status {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .status-ada { color: #16a34a; }
    .status-tiada { color: #94a3b8; }

    .btn-hub {
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
    }

    .btn-hub-edit {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-hub-edit:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .btn-hub-add {
        background: #2563eb;
        color: white;
    }

    .btn-hub-add:hover {
        background: #1d4ed8;
    }

    .section-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 40px 0;
        position: relative;
    }

    .section-divider span {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #f8fafc;
        padding: 0 20px;
        color: #64748b;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>

<div style="margin-bottom: 32px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">
        🛠️ Kemas Kini Laporan
    </h2>
    <p style="color: #64748b; font-size: 15px;">Kemas kini butiran sedia ada atau tambah bahagian yang tertinggal.</p>
</div>

{{-- SECTION HUB --}}
<div class="hub-grid">
    {{-- DISIPLIN --}}
    <div class="hub-card">
        <div class="hub-icon" style="background: #eff6ff;">⚡</div>
        <div class="hub-title">Disiplin</div>
        <div class="hub-status {{ $sectionStatus['disiplin'] ? 'status-ada' : 'status-tiada' }}">
            {{ $sectionStatus['disiplin'] ? '✓ Sudah Ada' : '○ Belum Ada' }}
        </div>
        <a href="{{ route('laporan.disiplin.create', $laporan->id_laporan) }}?from=hub" class="btn-hub {{ $sectionStatus['disiplin'] ? 'btn-hub-edit' : 'btn-hub-add' }}">
            {{ $sectionStatus['disiplin'] ? 'Kemaskini' : 'Tambah Bahagian' }}
        </a>
    </div>

    {{-- KEROSAKAN --}}
    <div class="hub-card">
        <div class="hub-icon" style="background: #fff7ed;">🔧</div>
        <div class="hub-title">Kerosakan</div>
        <div class="hub-status {{ $sectionStatus['kerosakan'] ? 'status-ada' : 'status-tiada' }}">
            {{ $sectionStatus['kerosakan'] ? '✓ Sudah Ada' : '○ Belum Ada' }}
        </div>
        <a href="{{ route('laporan.kerosakan.create', $laporan->id_laporan) }}?from=hub" class="btn-hub {{ $sectionStatus['kerosakan'] ? 'btn-hub-edit' : 'btn-hub-add' }}">
            {{ $sectionStatus['kerosakan'] ? 'Kemaskini' : 'Tambah Bahagian' }}
        </a>
    </div>

    {{-- PELAJAR SAKIT --}}
    <div class="hub-card">
        <div class="hub-icon" style="background: #fef2f2;">🏥</div>
        <div class="hub-title">Pelajar Sakit</div>
        <div class="hub-status {{ $sectionStatus['pelajar_sakit'] ? 'status-ada' : 'status-tiada' }}">
            {{ $sectionStatus['pelajar_sakit'] ? '✓ Sudah Ada' : '○ Belum Ada' }}
        </div>
        <a href="{{ route('laporan.pelajarsakit.create', $laporan->id_laporan) }}?from=hub" class="btn-hub {{ $sectionStatus['pelajar_sakit'] ? 'btn-hub-edit' : 'btn-hub-add' }}">
            {{ $sectionStatus['pelajar_sakit'] ? 'Kemaskini' : 'Tambah Bahagian' }}
        </a>
    </div>

    {{-- DEWAN MAKAN --}}
    <div class="hub-card">
        <div class="hub-icon" style="background: #f0fdf4;">🍽️</div>
        <div class="hub-title">Dewan Makan</div>
        <div class="hub-status {{ $sectionStatus['dewan_makan'] ? 'status-ada' : 'status-tiada' }}">
            {{ $sectionStatus['dewan_makan'] ? '✓ Sudah Ada' : '○ Belum Ada' }}
        </div>
        <a href="{{ route('laporan.dewanmakan.create', $laporan->id_laporan) }}?from=hub" class="btn-hub {{ $sectionStatus['dewan_makan'] ? 'btn-hub-edit' : 'btn-hub-add' }}">
            {{ $sectionStatus['dewan_makan'] ? 'Kemaskini' : 'Tambah Bahagian' }}
        </a>
    </div>
</div>

<div class="section-divider">
    <span>Butiran Laporan Dorm</span>
</div>

{{-- DORM FORM (EXISTING) --}}
<form action="{{ route('laporan.updateDorm', $laporan->id_laporan) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- TARIKH & EXCO --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background:white;padding:24px;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.05);border:1px solid #f1f5f9">
            <h3 style="margin:0 0 16px 0;font-size:18px;font-weight:700">📅 Maklumat Masa</h3>
            <div style="display:flex;flex-direction:column;gap:16px">
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:6px;font-size:14px;color:#64748b">Tarikh Laporan</label>
                    <input type="date" name="tarikh_laporan" required
                        value="{{ is_string($laporan->tarikh_laporan) ? $laporan->tarikh_laporan : $laporan->tarikh_laporan->format('Y-m-d') }}"
                        style="padding:10px;border-radius:10px;border:1px solid #e2e8f0;width:100%;font-weight:600">
                </div>
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:6px;font-size:14px;color:#64748b">Tarikh Hantar (Auto)</label>
                    <input type="text" readonly disabled
                        value="{{ $laporan->tarikh_hantar ? (\Carbon\Carbon::parse($laporan->tarikh_hantar)->format('d/m/Y h:i A')) : 'Belum dihantar' }}"
                        style="padding:10px;border-radius:10px;border:1px solid #e2e8f0;width:100%;background:#f8fafc;color:#94a3b8">
                </div>
            </div>
        </div>

        <div style="background:white;padding:24px;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.05);border:1px solid #f1f5f9">
            <h3 style="margin:0 0 16px 0;font-size:18px;font-weight:700">👥 EXCO Bertugas</h3>
            <div style="display:flex;flex-direction:column;gap:16px">
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:6px;font-size:14px;color:#64748b">EXCO Pertama</label>
                    <select name="exco1" required style="padding:10px;border-radius:10px;border:1px solid #e2e8f0;width:100%;font-weight:600">
                        @foreach($senaraiExco as $exco)
                            <option value="{{ $exco->no_ic }}" @if(isset($excos[0]) && $excos[0] === $exco->no_ic) selected @endif>
                                {{ $exco->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:6px;font-size:14px;color:#64748b">EXCO Kedua</label>
                    <select name="exco2" required style="padding:10px;border-radius:10px;border:1px solid #e2e8f0;width:100%;font-weight:600">
                        @foreach($senaraiExco as $exco)
                            <option value="{{ $exco->no_ic }}" @if(isset($excos[1]) && $excos[1] === $exco->no_ic) selected @endif>
                                {{ $exco->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- DORM BLOCKS --}}
    @php
        $blocks = ['A', 'B', 'C', 'D'];
        $dormByBlock = [];
        foreach ($blocks as $blk) { $dormByBlock[$blk] = []; }
        foreach ($dorms as $dorm) {
            $block = $dorm->blok ?? 'A';
            if (!isset($dormByBlock[$block])) $dormByBlock[$block] = [];
            $dormByBlock[$block][] = $dorm;
        }
    @endphp

    @foreach($dormByBlock as $block => $dormList)
        @if(count($dormList) > 0)
            <div style="display: flex; align-items: center; gap: 12px; margin: 32px 0 16px 0;">
                <div style="width: 8px; height: 24px; background: #2563eb; border-radius: 4px;"></div>
                <h3 style="font-size: 20px; font-weight: 800; color: #1e293b;">Blok {{ $block }}</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; margin-bottom: 30px;">
                @foreach($dormList as $dorm)
                    @php
                        $butiran = $butiranByDorm[$dorm->id_dorm] ?? null;
                        $selectedKategori = $butiran ? ($butiran->data_tambahan['kategori_kebersihan'] ?? 'Bersih') : 'Bersih';
                        $absentList = $butiran ? ($butiran->data_tambahan['tidak_hadir'] ?? []) : [];
                        if (is_string($absentList)) { $absentList = [$absentList]; }
                        $absentText = is_array($absentList) ? implode(', ', $absentList) : '';
                    @endphp

                    <div style="background:white;padding:24px;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.05);border:1px solid #f1f5f9">
                        <h4 style="margin:0 0 16px 0;font-size:17px;font-weight:700;color:#334155;border-bottom:1px solid #f1f5f9;padding-bottom:12px">
                            🏠 {{ $dorm->nama_dorm }}
                        </h4>

                        <label style="font-weight:600;margin-bottom:8px;display:block;font-size:14px;color:#64748b">Kategori Kebersihan</label>
                        <select name="kategori[{{ $dorm->id_dorm }}]" required
                                style="padding:10px;border-radius:10px;width:100%;margin-bottom:16px;border:1px solid #e2e8f0;font-weight:600">
                            <option value="Bersih" @if($selectedKategori === 'Bersih') selected @endif>✨ Bersih</option>
                            <option value="Sederhana" @if($selectedKategori === 'Sederhana') selected @endif>👌 Sederhana</option>
                            <option value="Kotor" @if($selectedKategori === 'Kotor') selected @endif>⚠️ Kotor</option>
                        </select>

                        <label style="font-weight:600;display:block;margin-bottom:8px;font-size:14px;color:#64748b">Pelajar Tidak Hadir</label>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; max-height: 180px; overflow-y: auto;">
                            @if($dorm->pelajars->count() > 0)
                                @foreach($dorm->pelajars as $student)
                                    @php
                                        $isAbsent = in_array($student->no_ic, $absentList);
                                    @endphp
                                    <label style="display: flex; align-items: center; gap: 10px; padding: 6px 8px; cursor: pointer; font-size: 14px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="absent[{{ $dorm->id_dorm }}][]" value="{{ $student->no_ic }}" 
                                               @if($isAbsent) checked @endif
                                               style="width: 18px; height: 18px; accent-color: #2563eb;">
                                        <span style="{{ $isAbsent ? 'font-weight: 700; color: #2563eb;' : '' }}">{{ $student->nama }}</span>
                                    </label>
                                @endforeach
                            @else
                                <p style="color: #94a3b8; font-size: 13px; font-style: italic; text-align: center; padding: 20px 0;">Tiada pelajar didaftarkan.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach

    <div style="position: sticky; bottom: 20px; background: white; padding: 16px 24px; border-radius: 16px; 
                box-shadow: 0 -4px 30px rgba(0,0,0,0.1); display: flex; justify-content: flex-end; gap: 16px; z-index: 100; border: 1px solid #f1f5f9;">
        <a href="{{ route('homepage') }}" style="background: #f1f5f9; color: #475569; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 700;">
            Batal
        </a>
        <button type="submit" style="background: #2563eb; color: white; padding: 12px 32px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.2)">
            Simpan Perubahan Dorm
        </button>
    </div>

</form>
@endsection
