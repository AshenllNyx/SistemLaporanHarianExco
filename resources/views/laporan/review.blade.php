@extends('layouts.app')

@section('title','Semak Laporan')

@section('content')
<h2 style="font-size:22px;font-weight:700">Semak Laporan</h2>

@if(session('success'))
    <div style="background:#ecfdf5;padding:10px;border-radius:8px;margin-bottom:12px">{{ session('success') }}</div>
@endif

<div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
    <h3>Maklumat Exco: {{ $laporan->nama_exco }} ({{ $laporan->no_ic }})</h3>
    <p>Tarikh laporan: {{ $laporan->tarikh_laporan }}</p>

    <hr>

    @foreach($laporan->butiranLaporans as $butiran)
        <div style="margin-bottom:14px">
            <h4 style="margin:0 0 6px 0">
                @if($butiran->jenis_butiran === 'dorm')
                    Dorm: {{ $butiran->dorm->nama_dorm ?? 'Dorm '.$butiran->id_dorm }}
                @else
                    {{ ucfirst($butiran->jenis_butiran) }}
                @endif
            </h4>

            @php $data = $butiran->data_tambahan ?? []; @endphp

            @if($butiran->jenis_butiran === 'dorm')
                <p><strong>Kategori kebersihan:</strong> {{ $data['kategori_kebersihan'] ?? '-' }}</p>
                <p><strong>Nama tidak hadir:</strong>
                    @if(!empty($data['tidak_hadir']))
                        <ul>
                            @foreach($data['tidak_hadir'] as $n)
                                <li>{{ $n }}</li>
                            @endforeach
                        </ul>
                    @else
                        Tiada
                    @endif
                </p>
            @elseif($butiran->jenis_butiran === 'disiplin')
                <p><strong>Jenis Kesalahan:</strong> {{ $data['jenis_kesalahan'] ?? $butiran->deskripsi_isu }}</p>
                <p><strong>Pelajar:</strong>
                    @if(!empty($data['pelajar']))
                        <ul>
                            @foreach($data['pelajar'] as $p)
                                <li>{{ $p }}</li>
                            @endforeach
                        </ul>
                    @else
                        Tiada
                    @endif
                </p>
                <p><strong>Tindakan:</strong> {{ $data['tindakan'] ?? '—' }}</p>
                <p><strong>Catatan:</strong> {{ $data['catatan'] ?? '—' }}</p>
            @else
                <p>{{ $butiran->deskripsi_isu ?? '-' }}</p>
            @endif
        </div>
    @endforeach

    <form method="POST" action="{{ route('laporan.submit', ['laporan' => $laporan->id_laporan]) }}">
        @csrf
        <div style="display:flex;gap:12px;justify-content:flex-end">
            <a href="{{ route('laporan.create') }}" style="padding:8px 12px;border-radius:8px;background:#f3f4f6;color:#111;text-decoration:none">Edit</a>
            <button type="submit" style="padding:8px 14px;border-radius:8px;background:#10b981;color:white;border:none">Hantar Laporan</button>
        </div>
    </form>
</div>
@endsection
