@extends('layouts.app')

@section('title', 'Borang Laporan Harian - Dorm')

@section('content')
<h2 style="font-size:22px;font-weight:700;margin-bottom:16px">
    Borang Laporan Harian (Dorm)
</h2>

<form action="{{ route('laporan.storeDorm') }}" method="POST">
    @csrf

    {{-- ===========================
         PILIH 2 EXCO BERTUGAS
       =========================== --}}
    <div style="background:white;padding:20px;border-radius:12px;margin-bottom:20px;
                box-shadow:0 6px 18px rgba(2,6,23,0.06)">
        <h3 style="margin:0 0 10px 0;font-size:18px;font-weight:700">
            EXCO Bertugas Hari Ini
        </h3>

        <label style="font-weight:600;display:block;margin-top:10px">EXCO 1</label>
        <select name="exco1" required
            style="padding:10px;border-radius:8px;width:260px;margin-bottom:14px">
            <option value="">-- Pilih EXCO Pertama --</option>
            @foreach($senaraiExco as $exco)
                <option value="{{ $exco->no_ic }}">{{ $exco->name }}</option>
            @endforeach
        </select>

        <label style="font-weight:600;display:block;">EXCO 2</label>
        <select name="exco2" required
            style="padding:10px;border-radius:8px;width:260px;margin-bottom:14px">
            <option value="">-- Pilih EXCO Kedua --</option>
            @foreach($senaraiExco as $exco)
                <option value="{{ $exco->no_ic }}">{{ $exco->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- ===========================
         BLOK DORM
       =========================== --}}
    @php
        $blocks = ['A', 'B', 'C', 'D'];
        $dormByBlock = [];
        foreach ($blocks as $blk) {
            $dormByBlock[$blk] = [];
        }
        foreach ($dorms as $dorm) {
            $block = $dorm->blok ?? 'A'; // default A kalau tiada blok
            if (!isset($dormByBlock[$block])) $dormByBlock[$block] = [];
            $dormByBlock[$block][] = $dorm;
        }
    @endphp

    @foreach($dormByBlock as $block => $dormList)
        <h3 style="font-size:20px;font-weight:700;margin-top:25px;">
            Blok {{ $block }}
        </h3>

        <div style="display:flex;flex-direction:column;gap:20px;margin-bottom:20px">
            @foreach($dormList as $dorm)
                <div style="background:white;padding:20px;border-radius:12px;
                            box-shadow:0 6px 18px rgba(2,6,23,0.06)">
                    <h4 style="margin:0 0 10px 0;font-size:18px;font-weight:700">
                        {{ $dorm->nama_dorm }}
                    </h4>

                    {{-- Kategori Kebersihan --}}
                    <label style="font-weight:600;margin-bottom:6px;display:block">
                        Kategori Kebersihan
                    </label>
                    <select name="kategori[{{ $dorm->id_dorm }}]" required
                            style="padding:10px;border-radius:8px;width:260px;margin-bottom:14px">
                        <option value="Bersih">Bersih</option>
                        <option value="Sederhana">Sederhana</option>
                        <option value="Kotor">Kotor</option>
                    </select>

                    {{-- Senarai Pelajar --}}
                    <label style="font-weight:600;display:block;margin-bottom:6px">
                        Nama Tidak Hadir
                    </label>

                    @php
                        $pelajar = is_array($dorm->senarai_pelajar) ? $dorm->senarai_pelajar : json_decode($dorm->senarai_pelajar, true);
                    @endphp

                    @if(is_array($pelajar) && count($pelajar) > 0)
                        <div style="display:flex;flex-wrap:wrap;gap:12px">
                            @foreach($pelajar as $p)
                                @php
                                    $nama = $p['nama'] ?? 'Pelajar Tidak Dikenal';
                                    $ic   = $p['no_ic'] ?? null;
                                @endphp
                                <label style="display:flex;align-items:center;gap:8px;
                                              background:#f9fafb;padding:6px 10px;
                                              border-radius:8px;border:1px solid #e5e7eb">
                                    <input type="checkbox" name="absent[{{ $dorm->id_dorm }}][]" value="{{ $ic }}">
                                    {{ $nama }}
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p style="color:#6b7280">Tiada pelajar dalam dorm ini.</p>
                    @endif

                </div>
            @endforeach
        </div>
    @endforeach

    <div style="display:flex;justify-content:flex-end;margin-top:20px;gap:10px">
        <a href="{{ route('homepage') }}"
           style="background:#e5e7eb;padding:10px 14px;border-radius:8px;
                  text-decoration:none;color:#111">
            Batal
        </a>

        <button type="submit"
                style="background:linear-gradient(90deg,#2563eb,#4f46e5);
                       color:white;padding:10px 16px;border-radius:8px;border:none">
            Next / Semak
        </button>
    </div>

</form>
@endsection
