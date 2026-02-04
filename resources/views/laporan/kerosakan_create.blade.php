@extends('layouts.app')

@section('title','Borang Laporan Kerosakan')

@section('content')
@include('components.report-steps', ['currentStep' => 3])

<div style="margin-bottom: 32px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">
        🔧 Laporan Kerosakan
    </h2>
    <p style="color: #64748b; font-size: 15px;">Mencatat kerosakan kemudahan atau aset di kawasan asrama.</p>
</div>

<div style="background:white;padding:32px;border-radius:24px;box-shadow:0 10px 40px rgba(0,0,0,0.05);border:1px solid #f1f5f9">
    <form method="POST" action="{{ route('laporan.kerosakan.store') }}">
        @csrf
        <input type="hidden" name="id_laporan" value="{{ $laporan->id_laporan }}">
        <input type="hidden" name="from" value="{{ $from ?? '' }}">

        <div style="margin-bottom:24px">
            <label style="font-weight:700;display:block;margin-bottom:12px;color:#1e293b;font-size:15px">🏢 Pilih Dorm</label>
            <select name="id_dorm" required style="width:100%;padding:12px;border-radius:12px;border:1px solid #e2e8f0;font-size:14px;font-weight:600">
                <option value="">-- Pilih Dorm Terlibat --</option>
                @foreach($dorms as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 24px;">
            <div>
                <label style="font-weight:700;display:block;margin-bottom:8px;color:#1e293b;font-size:15px">🔧 Jenis Kerosakan</label>
                <input type="text" name="jenis_kerosakan" required 
                       style="width:100%;padding:12px;border-radius:12px;border:1px solid #e2e8f0;font-size:14px" 
                       placeholder="Cth: Lampu terbakar, Pintu tidak boleh kunci" />
            </div>

            <div>
                <label style="font-weight:700;display:block;margin-bottom:8px;color:#1e293b;font-size:15px">📍 Lokasi Spesifik</label>
                <input type="text" name="lokasi" 
                       style="width:100%;padding:12px;border-radius:12px;border:1px solid #e2e8f0;font-size:14px" 
                       placeholder="Cth: Bilik 101, Tandas Bawah" />
            </div>
        </div>

        <div style="margin-bottom:32px">
            <label style="font-weight:700;display:block;margin-bottom:8px;color:#1e293b;font-size:15px">📝 Catatan Tambahan</label>
            <textarea name="catatan" rows="4" 
                      style="width:100%;padding:12px;border-radius:12px;border:1px solid #e2e8f0;font-size:14px;resize:none"
                      placeholder="Masukkan butiran lanjut mengenai kerosakan..."></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 32px; flex-wrap: wrap; gap: 20px;">
            <a href="{{ $from === 'hub' ? route('laporan.edit', $laporan->id_laporan) : route('laporan.pelajarsakit.soalan', $laporan->id_laporan) }}" 
               class="btn-back"
               style="color: #64748b; font-weight: 700; text-decoration: none; font-size: 15px; min-width: 150px; text-align: center;">
                {{ $from === 'hub' ? '⬅️ Batal & Kembali' : 'Langkau Bahagian Ini' }}
            </a>
            
            <button type="submit" 
                    class="btn btn-submit"
                    style="background: #f59e0b; color: white; padding: 14px 32px; border-radius: 14px; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(245,158,11,0.2); font-size: 15px; flex: 1; min-width: 250px;">
                {{ $from === 'hub' ? 'Simpan & Kembali ke Hub' : 'Simpan dan Teruskan ➔' }}
            </button>
        </div>
    </form>
</div>
@endsection