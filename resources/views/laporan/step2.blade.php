@extends('layouts.app')

@section('title','Pilih Laporan Seterusnya')

@section('content')
<h2 style="font-size:22px;font-weight:700">Adakah terdapat kesalahan disiplin?</h2>

<div style="background:white;padding:20px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
    <p>Sila pilih sama ada anda ingin mencatat laporan disiplin untuk laporan ini.</p>

    <div style="display:flex;gap:12px;margin-top:12px">
        {{-- Button YA --}}
        <form method="GET" action="{{ route('laporan.disiplin.create', $laporan->id_laporan) }}" style="display:inline">
            <button type="submit" style="padding:10px 14px;border-radius:8px;background:#2563eb;color:white;border:none;cursor:pointer">Ya</button>
        </form>

        {{-- Button TIDAK --}}
        <form method="GET" action="{{ route('laporan.kerosakan.soalan', $laporan->id_laporan) }}" style="display:inline">
            <button type="submit" style="padding:10px 14px;border-radius:8px;background:#e5e7eb;color:#111;border:none;cursor:pointer">Tidak</button>
        </form>
    </div>
</div>
@endsection
