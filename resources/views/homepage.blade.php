@extends('layouts.app')

@section('content')

@php
    // fallback (elak Undefined variable)
    $laporans = $laporans ?? collect();
    $laporanHantarSemula = $laporanHantarSemula ?? collect();
@endphp

<style>
    .wrap { width: 95%; max-width:1100px; margin: 24px auto; }
    .title { font-size:24px; font-weight:800; margin-bottom:12px; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap:14px; margin-bottom:18px; }
    .card { background:#fff; padding:14px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.04); border-left:4px solid #2563eb; }
    .card.red { border-left-color:#ef4444; background:#fff5f5; }
    .muted { color:#6b7280; font-size:14px; }
    .btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#2563eb; color:#fff; text-decoration:none; font-weight:600; }
    .btn.gray { background:#9ca3af; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #eef2ff; text-align:left; }
    .badge { padding:6px 8px; border-radius:8px; font-weight:700; font-size:13px; }
    .badge.green { background:#d1fae5; color:#065f46; }
    .badge.yellow { background:#fff7ed; color:#92400e; }
    .badge.red { background:#fee2e2; color:#991b1b; }
</style>

<div class="wrap">

    <div class="title">SISTEM LAPORAN HARIAN EXCO</div>

    {{-- Statistik ringkas --}}
    <div class="grid" style="grid-template-columns:repeat(4,1fr); margin-bottom:20px;">
        <div class="card">
            <div class="muted">Jumlah Laporan</div>
            <div style="font-size:20px;font-weight:800">{{ $laporans->count() }}</div>
        </div>

        <div class="card">
            <div class="muted">Draf</div>
            <div style="font-size:20px;font-weight:800">{{ $laporans->where('status_laporan','draf')->count() }}</div>
        </div>

        <div class="card">
            <div class="muted">Dihantar</div>
            <div style="font-size:20px;font-weight:800">{{ $laporans->where('status_laporan','dihantar')->count() }}</div>
        </div>

        <div class="card">
            <div class="muted">Perlu Hantar Semula</div>
            <div style="font-size:20px;font-weight:800">{{ $laporanHantarSemula->count() }}</div>
        </div>
    </div>

    {{-- Laporan perlu dihantar semula --}}
    <h3 style="margin-bottom:8px">Laporan Perlu Dihantar Semula</h3>

    @if($laporanHantarSemula->isEmpty())
        <p class="muted">Tiada laporan perlu dihantar semula.</p>
    @else
        <div class="grid">
            @foreach($laporanHantarSemula as $lap)
                <div class="card red">
                    <div style="font-weight:800">Tarikh: {{ $lap->tarikh_laporan ?? '-' }}</div>
                    <div class="muted">Status: {{ $lap->status_laporan }}</div>
                    <div style="margin-top:10px">
                        <a href="{{ route('laporan.review', $lap->id_laporan) }}" class="btn">Semak</a>
                        <a href="{{ url('/laporan/edit/'.$lap->id_laporan) }}" class="btn gray">Edit</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <hr style="margin:20px 0">

    {{-- Senarai laporan terkini --}}
    <h3 style="margin-bottom:8px">Senarai Laporan Terkini</h3>

    @if($laporans->isEmpty())
        <p class="muted">Tiada laporan direkodkan.</p>
    @else
        <div style="overflow-x:auto;background:white;padding:12px;border-radius:10px;box-shadow:0 6px 18px rgba(2,6,23,0.03)">
            <table>
                <thead>
                    <tr>
                        <th>Tarikh Laporan</th>
                        <th>Status</th>
                        <th>Butiran</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporans as $lap)
                    <tr>
                        <td>{{ $lap->tarikh_laporan ?? $lap->created_at->toDateString() }}</td>
                        <td>
                            @php $s = $lap->status_laporan; @endphp
                            @if($s == 'dihantar') <span class="badge green">Dihantar</span>
                            @elseif(in_array($s,['hantar_semula','perlu_hantar_semula','tolak'])) <span class="badge red">Perlu Semula</span>
                            @elseif($s == 'draf') <span class="badge yellow">Draf</span>
                            @else <span class="badge">{{ $s }}</span>
                            @endif
                        </td>
                        <td>
                            {{-- paparkan ringkasan butiran jika ada --}}
                            @if($lap->butiranLaporans && $lap->butiranLaporans->count())
                                @foreach($lap->butiranLaporans as $b)
                                    @php
                                        $data = $b->data_tambahan ?? [];
                                        $dormName = $b->dorm->nama_dorm ?? ($b->id_dorm ? 'Dorm #'.$b->id_dorm : '-');
                                    @endphp
                                    <div style="font-size:13px;margin-bottom:6px">
                                        @if($b->jenis_butiran === 'dorm')
                                            <strong>Dorm {{ $dormName }}:</strong>
                                            Kebersihan: {{ $data['kategori_kebersihan'] ?? '-' }}.
                                            @php
                                                $absent = $data['tidak_hadir'] ?? [];
                                                if (is_string($absent)) {
                                                    $absentList = $absent;
                                                } elseif(is_array($absent) && count($absent)) {
                                                    $absentList = implode(', ', $absent);
                                                } else {
                                                    $absentList = '-';
                                                }
                                            @endphp
                                            Tidak hadir: {{ $absentList }}
                                        @elseif($b->jenis_butiran === 'disiplin')
                                            <strong>Disiplin:</strong>
                                            Kesalahan: {{ $data['jenis_kesalahan'] ?? $b->deskripsi_isu ?? '-' }}.
                                            Pelajar: {{ isset($data['pelajar']) && is_array($data['pelajar']) ? count($data['pelajar']) : '-' }}
                                        @elseif($b->jenis_butiran === 'kerosakan')
                                            <strong>Kerosakan ({{ $dormName }}):</strong>
                                            {{ $data['jenis_kerosakan'] ?? $b->deskripsi_isu ?? '-' }}
                                            @if(!empty($data['lokasi'])) di {{ $data['lokasi'] }} @endif
                                        @elseif($b->jenis_butiran === 'pelajar_sakit')
                                            <strong>Pelajar Sakit:</strong>
                                            {{ $data['jenis_sakit'] ?? $b->deskripsi_isu ?? '-' }}.
                                            Pelajar: {{ isset($data['pelajar']) && is_array($data['pelajar']) ? count($data['pelajar']) : '-' }}
                                        @elseif($b->jenis_butiran === 'dewan_makan')
                                            <strong>Dewan Makan:</strong>
                                            {{ $data['jenis_isu'] ?? $b->deskripsi_isu ?? '-' }}
                                            @if(!empty($data['masa_makan'])) ({{ $data['masa_makan'] }}) @endif
                                        @else
                                            <strong>{{ ucfirst($b->jenis_butiran) }}:</strong>
                                            {{ is_array($data) ? json_encode($data) : ($data ?? $b->deskripsi_isu ?? '-') }}
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <span class="muted">Tiada butiran</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <a href="{{ route('laporan.review', $lap->id_laporan) }}" class="btn">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

@endsection
