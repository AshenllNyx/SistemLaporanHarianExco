@extends('layouts.app')

@section('title','Tambah Dorm')

@section('content')
<h2 style="font-size:22px;font-weight:700;margin-bottom:16px">Tambah Dorm Baharu</h2>

<div style="background:white;padding:20px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
    <form method="POST" action="{{ route('dorms.store') }}">
        @csrf

        <div style="margin-bottom:14px">
            <label style="font-weight:600;display:block;margin-bottom:6px">Nama Dorm</label>
            <input type="text" name="nama_dorm" value="{{ old('nama_dorm') }}" required
                   style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px">
        </div>

        <div style="margin-bottom:14px">
            <label style="font-weight:600;display:block;margin-bottom:6px">Blok</label>
            <input type="text" name="blok" value="{{ old('blok') }}" placeholder="A / B / C / D" required
                   style="width:100%; max-width: 400px; padding:12px;border:1px solid #d1d5db;border-radius:10px;font-size:16px">
        </div>

        <div style="margin-bottom:14px">
            <label style="font-weight:600;display:block;margin-bottom:6px">Kapasiti (pilihan)</label>
            <input type="number" name="capacity" value="{{ old('capacity') }}"
                   style="width:100%; max-width: 400px; padding:12px;border:1px solid #d1d5db;border-radius:10px;font-size:16px">
            <p style="font-size:13px;color:#6b7280;margin-top:6px">Dibiarkan kosong jika tiada.</p>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;flex-wrap:wrap">
            <a href="{{ route('dorms.index') }}" class="btn-cancel" style="flex:1; min-width:120px; text-align:center; padding:12px;border-radius:10px;background:#f3f4f6;color:#374151;text-decoration:none;font-weight:600">Batal</a>
            <button type="submit" style="flex:1; min-width:120px; padding:12px;border-radius:10px;background:#2563eb;color:white;border:none;cursor:pointer;font-weight:700;box-shadow: 0 4px 12px rgba(37,99,235,0.2)">Simpan</button>
        </div>
    </form>
</div>
@endsection

