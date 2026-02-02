@extends('layouts.app')

@section('title', 'Borang Laporan Harian - Dorm')

@section('content')
@include('components.report-steps', ['currentStep' => 1])

<div style="margin-bottom: 24px;">
    <h2 style="font-size: 24px; font-weight: 800; letter-spacing: -0.02em; color: var(--text-main);">
        Borang Laporan Harian
    </h2>
    <p style="color: var(--text-muted); font-size: 14px;">Sila lengkapkan maklumat kebersihan dan kehadiran untuk setiap dorm.</p>
</div>

<form action="{{ route('laporan.storeDorm') }}" method="POST">
    @csrf

    <div class="card" style="border-left: 4px solid var(--accent);">
        <h3 style="margin-bottom: 16px; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 20px;">👤</span> EXCO Bertugas Hari Ini
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label>EXCO Pertama</label>
                <select name="exco1" required>
                    <option value="">-- Pilih EXCO Pertama --</option>
                    @foreach($senaraiExco as $exco)
                        <option value="{{ $exco->no_ic }}">{{ $exco->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>EXCO Kedua</label>
                <select name="exco2" required>
                    <option value="">-- Pilih EXCO Kedua --</option>
                    @foreach($senaraiExco as $exco)
                        <option value="{{ $exco->no_ic }}">{{ $exco->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @php
        $blocks = ['A', 'B', 'C', 'D'];
        $dormByBlock = [];
        foreach ($blocks as $blk) {
            $dormByBlock[$blk] = [];
        }
        foreach ($dorms as $dorm) {
            $block = $dorm->blok ?? 'A';
            if (!isset($dormByBlock[$block])) $dormByBlock[$block] = [];
            $dormByBlock[$block][] = $dorm;
        }
    @endphp

    @foreach($dormByBlock as $block => $dormList)
        @if(count($dormList) > 0)
            <div style="margin: 32px 0 16px 0; display: flex; align-items: center; gap: 12px;">
                <div style="height: 1px; flex-grow: 1; background: var(--border);"></div>
                <h3 style="font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted);">
                    Blok {{ $block }}
                </h3>
                <div style="height: 1px; flex-grow: 1; background: var(--border);"></div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px; margin-bottom: 32px;">
                @foreach($dormList as $dorm)
                    <div class="card" style="margin-bottom: 0;">
                        <h4 style="margin-bottom: 16px; font-size: 18px; font-weight: 700; color: var(--accent);">
                            {{ $dorm->nama_dorm }}
                        </h4>

                        <div class="form-group">
                            <label>Kategori Kebersihan</label>
                            <select name="kategori[{{ $dorm->id_dorm }}]" required>
                                <option value="Bersih">✨ Bersih</option>
                                <option value="Sederhana">👌 Sederhana</option>
                                <option value="Kotor">⚠️ Kotor</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pelajar Tidak Hadir</label>
                            <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 12px; padding: 12px; max-height: 180px; overflow-y: auto;">
                                @if($dorm->pelajars->count() > 0)
                                    @foreach($dorm->pelajars as $student)
                                        <label style="display: flex; align-items: center; gap: 10px; padding: 6px 8px; cursor: pointer; font-size: 14px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                            <input type="checkbox" name="absent[{{ $dorm->id_dorm }}][]" value="{{ $student->no_ic }}" style="width: 18px; height: 18px; accent-color: var(--accent);">
                                            <span>{{ $student->nama }}</span>
                                        </label>
                                    @endforeach
                                @else
                                    <p style="color: var(--muted); font-size: 13px; font-style: italic; text-align: center; padding: 20px 0;">Tiada pelajar didaftarkan.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach

    <div style="display: flex; justify-content: flex-end; margin-top: 40px; gap: 12px; padding-bottom: 40px;">
        <a href="{{ route('homepage') }}" class="btn btn-secondary">
            Batal
        </a>

        <button type="submit" class="btn btn-primary">
            Seterusnya: Laporan Disiplin ➔
        </button>
    </div>
</form>
@endsection
