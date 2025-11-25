@extends('layouts.app')

@section('title','Borang Laporan Disiplin')

@section('content')
<h2 style="font-size:22px;font-weight:700">Borang Laporan Disiplin</h2>

<div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
    <form method="POST" action="{{ route('laporan.disiplin.store') }}">
        @csrf

        <input type="hidden" name="id_laporan" value="{{ $laporan->id_laporan }}">

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Pilih Pelajar (boleh pilih lebih daripada satu)</label>
            <select name="pelajar[]" multiple required style="width:100%;padding:10px;border-radius:8px">
                @foreach($students as $ic => $p)
                    <option value="{{ $ic }}">{{ $p['nama'] }} — {{ $ic }}</option>
                @endforeach
            </select>
            <small style="color:#6b7280">Tekan Ctrl/Command untuk pilih lebih daripada satu.</small>
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Jenis Kesalahan</label>
            <input type="text" name="jenis_kesalahan" required style="width:100%;padding:10px;border-radius:8px" placeholder="Contoh: Kerosakan harta, Merokok, Ponteng" />
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Tindakan</label>
            <input type="text" name="tindakan" style="width:100%;padding:10px;border-radius:8px" placeholder="Contoh: Surat amaran, Lapor warden" />
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Catatan</label>
            <textarea name="catatan" rows="4" style="width:100%;padding:10px;border-radius:8px"></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px">
            <a href="{{ route('laporan.review', $laporan->id_laporan) }}" style="padding:10px 14px;border-radius:8px;background:#e5e7eb;color:#111;text-decoration:none">Lewat</a>
            <button type="submit" style="padding:10px 14px;border-radius:8px;background:#2563eb;color:white;border:none">Simpan & Semak</button>
        </div>
    </form>
</div>
@endsection
