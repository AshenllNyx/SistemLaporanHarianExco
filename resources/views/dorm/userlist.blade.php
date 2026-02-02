@extends('layouts.app')

@section('title', 'Senarai Dorm')

@section('content')
<style>
    .wrap { width: 95%; max-width:1200px; margin: 24px auto; }
    .title { font-size:24px; font-weight:800; margin-bottom:20px; }
    .block-section { margin-bottom:30px; }
    .block-title { font-size:18px; font-weight:700; color:#2563eb; padding:12px 16px; background:#eff6ff; border-left:4px solid #2563eb; border-radius:4px; margin-bottom:16px; }
    .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px; }
    .dorm-card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); border-left:5px solid #3b82f6; transition:transform 0.2s, box-shadow 0.2s; }
    .dorm-card:hover { transform:translateY(-4px); box-shadow:0 6px 16px rgba(0,0,0,0.12); }
    .dorm-name { font-size:18px; font-weight:700; margin:0 0 4px 0; color:#111; }
    .dorm-info { font-size:14px; color:#6b7280; margin:6px 0; }
    .dorm-stats { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:12px 0; }
    .stat { background:#f9fafb; padding:10px; border-radius:8px; text-align:center; }
    .stat-value { font-size:18px; font-weight:700; color:#2563eb; }
    .stat-label { font-size:12px; color:#6b7280; margin-top:4px; }
    .btn-group { display:flex; gap:6px; margin-top:12px; }
    .btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#2563eb; color:#fff; text-decoration:none; font-weight:600; font-size:13px; flex:1; text-align:center; }
    .btn:hover { background:#1d4ed8; }
    .btn-edit { background:#f59e0b; }
    .btn-edit:hover { background:#d97706; }
    .btn-delete { background:#ef4444; padding:0; }
    .btn-delete:hover { background:#dc2626; }
    .empty-state { text-align:center; padding:60px 20px; color:#6b7280; }
    .empty-icon { font-size:48px; margin-bottom:16px; }
    .header-action { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
</style>

<div class="wrap">
    <div class="header-action">
        <h1 class="title">Senarai Dorm</h1>
        @if(Auth::user()->level == 'admin')
            <a href="{{ route('dorms.create') }}" style="padding:10px 14px;border-radius:8px;background:#2563eb;color:white;text-decoration:none;font-weight:600">+ Tambah Dorm</a>
        @endif
    </div>

    @if(session('success'))
        <div style="background:#ecfdf3;border:1px solid #bbf7d0;padding:12px;border-radius:8px;color:#166534;margin-bottom:20px">
            {{ session('success') }}
        </div>
    @endif

    @php
        // Group dorms by block
        $dormsByBlock = $dorms->groupBy('blok')->sortKeys();
    @endphp

    @if($dorms->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🏢</div>
            <p style="font-size:18px; margin:0">Tiada dorm direkodkan</p>
            <p style="font-size:14px; margin:8px 0 0 0">Sila tunggu sehingga dorm ditambah oleh pentadbir.</p>
        </div>
    @else
        @foreach($dormsByBlock as $block => $blockDorms)
            <div class="block-section">
                <div class="block-title">📍 Blok {{ $block }}</div>
                <div class="grid">
                    @foreach($blockDorms as $dorm)
                        <div class="dorm-card">
                            <h2 class="dorm-name">{{ $dorm->nama_dorm }}</h2>

                            <div class="dorm-stats">
                                <div class="stat">
                                    <div class="stat-value">{{ $dorm->capacity ?? '-' }}</div>
                                    <div class="stat-label">Kapasiti</div>
                                </div>
                                @php
                                    $jumlah = $dorm->pelajars_count ?? $dorm->pelajars->count() ?? 0;
                                    $kosong = max(0, ($dorm->capacity ?? 0) - $jumlah);
                                @endphp
                                <div class="stat">
                                    <div class="stat-value">{{ $jumlah }}</div>
                                    <div class="stat-label">Anggota</div>
                                </div>
                            </div>

                            @if($jumlah > 0)
                                <p class="dorm-info" style="margin-top:12px; padding-top:12px; border-top:1px solid #f3f4f6;">
                                    💺 Kosong: <strong>{{ $kosong }}</strong>
                                </p>
                            @else
                                <p class="dorm-info" style="margin-top:12px; padding-top:12px; border-top:1px solid #f3f4f6; font-style:italic;">
                                    Tiada anggota ditambah
                                </p>
                            @endif

                            <div class="btn-group">
                                @if($jumlah > 0)
                                    <a href="{{ route('dorms.show', $dorm->id_dorm) }}" class="btn">Lihat Ahli</a>
                                @endif
                                @if(Auth::user()->level == 'admin')
                                    <a href="{{ route('dorms.edit', $dorm->id_dorm) }}" class="btn btn-edit">Edit</a>
                                    <form action="{{ route('dorms.destroy', $dorm->id_dorm) }}" method="POST" style="display:inline; flex:1" onsubmit="return confirm('Adakah anda pasti?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete" style="width:100%;">Padam</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
