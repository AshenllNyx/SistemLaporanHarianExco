@extends('layouts.app')

@section('title','Borang Laporan Kerosakan')

@section('content')
<h2 style="font-size:22px;font-weight:700">Borang Laporan Kerosakan</h2>

<div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
    <form method="POST" action="{{ route('laporan.kerosakan.store') }}">
        @csrf

        <input type="hidden" name="id_laporan" value="{{ $laporan->id_laporan }}">

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Pilih Dorm</label>
            <select name="id_dorm" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                <option value="">-- Pilih Dorm --</option>
                @foreach($dorms as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Jenis Kerosakan</label>
            <input type="text" name="jenis_kerosakan" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db" placeholder="Contoh: Tingkap pecah, Pintu rosak, Sinki tersumbat" />
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Lokasi</label>
            <input type="text" name="lokasi" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db" placeholder="Contoh: Bilik 101, Tandas tingkat 2" />
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Catatan</label>
            <textarea name="catatan" rows="4" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db" placeholder="Maklumat tambahan (jika ada)"></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px">
            <a href="{{ route('laporan.review', $laporan->id_laporan) }}" style="padding:10px 14px;border-radius:8px;background:#e5e7eb;color:#111;text-decoration:none">Lewat</a>
            <button type="submit" style="padding:10px 14px;border-radius:8px;background:#2563eb;color:white;border:none">Simpan & Semak</button>
        </div>
    </form>
</div>
@endsection