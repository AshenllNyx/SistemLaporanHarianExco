@extends('layouts.app')

@section('title','Borang Laporan Pelajar Sakit')

@section('content')
<h2 style="font-size:22px;font-weight:700">Borang Laporan Pelajar Sakit</h2>

<div style="background:white;padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
    <form method="POST" action="{{ route('laporan.pelajarsakit.store') }}">
        @csrf

        <input type="hidden" name="id_laporan" value="{{ $laporan->id_laporan }}">

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block;margin-bottom:8px">Pilih Pelajar</label>
            
            <div id="students-container">
                <!-- First student select (required) -->
                <div class="student-select-wrapper" style="margin-bottom:8px;display:flex;gap:8px;align-items:center">
                    <select name="pelajar[]" required style="flex:1;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                        <option value="">-- Pilih Pelajar --</option>
                        @foreach($students as $ic => $p)
                            <option value="{{ $ic }}">{{ $p['nama'] }} — {{ $ic }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="button" id="add-student-btn" style="padding:8px 12px;border-radius:8px;background:#10b981;color:white;border:none;font-size:14px;margin-top:8px">
                + Tambah Pelajar
            </button>
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Jenis Sakit / Simptom</label>
            <input type="text" name="jenis_sakit" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db" placeholder="Contoh: Demam, Sakit perut, Selesema" />
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Tindakan Diambil</label>
            <input type="text" name="tindakan" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db" placeholder="Contoh: Dibawa ke klinik, Rehat di bilik, Hubungi ibu bapa" />
        </div>

        <div style="margin-bottom:12px">
            <label style="font-weight:600;display:block">Catatan</label>
            <textarea name="catatan" rows="4" style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db" placeholder="Maklumat tambahan (jika ada)"></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px">
            <a href="{{ route('laporan.dewanmakan.soalan', $laporan->id_laporan) }}" style="padding:10px 14px;border-radius:8px;background:#e5e7eb;color:#111;text-decoration:none">Lewat</a>
            <button type="submit" style="padding:10px 14px;border-radius:8px;background:#2563eb;color:white;border:none">Simpan & Semak</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('students-container');
    const addBtn = document.getElementById('add-student-btn');
    
    const studentOptions = `
        <option value="">-- Pilih Pelajar --</option>
        @foreach($students as $ic => $p)
            <option value="{{ $ic }}">{{ $p['nama'] }} — {{ $ic }}</option>
        @endforeach
    `;

    addBtn.addEventListener('click', function() {
        const newSelect = document.createElement('div');
        newSelect.className = 'student-select-wrapper';
        newSelect.style.cssText = 'margin-bottom:8px;display:flex;gap:8px;align-items:center';
        
        newSelect.innerHTML = `
            <select name="pelajar[]" required style="flex:1;padding:10px;border-radius:8px;border:1px solid #d1d5db">
                ${studentOptions}
            </select>
            <button type="button" class="remove-student-btn" style="padding:8px 12px;border-radius:8px;background:#ef4444;color:white;border:none;font-size:14px">
                Buang
            </button>
        `;
        
        container.appendChild(newSelect);
        
        newSelect.querySelector('.remove-student-btn').addEventListener('click', function() {
            newSelect.remove();
        });
    });
});
</script>
@endsection