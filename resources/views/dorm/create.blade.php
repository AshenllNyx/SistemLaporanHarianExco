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
                   style="width:200px;padding:10px;border:1px solid #d1d5db;border-radius:8px">
        </div>

        <div style="margin-bottom:14px">
            <label style="font-weight:600;display:block;margin-bottom:6px">Kapasiti (pilihan)</label>
            <input type="number" name="capacity" value="{{ old('capacity') }}"
                   style="width:200px;padding:10px;border:1px solid #d1d5db;border-radius:8px">
        </div>

        <div style="margin-bottom:14px">
            <label style="font-weight:600;display:block;margin-bottom:6px">Senarai Pelajar (1 baris = 1 nama)</label>
            <textarea name="senarai_pelajar" rows="6"
                      style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;resize:vertical"
                      placeholder="Contoh:
Ali Bin Abu
Siti Binti Amin"></textarea>
            <p style="font-size:13px;color:#6b7280;margin-top:6px">Dibiarkan kosong jika tiada.</p>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px">
            <a href="{{ route('dorms.index') }}" class="btn gray" style="padding:10px 14px;border-radius:8px;background:#e5e7eb;color:#111;text-decoration:none">Batal</a>
            <button type="submit" class="btn" style="padding:10px 14px;border-radius:8px;background:#2563eb;color:white;border:none;cursor:pointer">Simpan</button>
        </div>
    </form>
</div>
@endsection

