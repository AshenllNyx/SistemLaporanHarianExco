@extends('layouts.app')

@section('content')

@php
    // Fallback untuk elak undefined variable
    $totalLaporans = $totalLaporans ?? 0;
    $draftCount = $draftCount ?? 0;
    $submittedCount = $submittedCount ?? 0;
    $resubmitCount = $resubmitCount ?? 0;
    $laporanByStatus = $laporanByStatus ?? collect();
    $recentLaporans = $recentLaporans ?? collect();
@endphp

<style>
    .admin-wrap { width: 95%; max-width:1200px; margin: 24px auto; }
    .admin-title { font-size:28px; font-weight:800; margin-bottom:8px; color:#1f2937; }
    .admin-subtitle { font-size:14px; color:#6b7280; margin-bottom:24px; }
    .stat-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:16px; margin-bottom:24px; }
    .stat-card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.04); border-left:5px solid #2563eb; }
    .stat-card.draft { border-left-color:#f59e0b; }
    .stat-card.submitted { border-left-color:#10b981; }
    .stat-card.resubmit { border-left-color:#ef4444; }
    .stat-label { color:#6b7280; font-size:13px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
    .stat-number { font-size:32px; font-weight:800; color:#1f2937; margin-top:8px; }
    .stat-subtext { font-size:12px; color:#9ca3af; margin-top:6px; }
    .chart-section { background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.04); margin-bottom:24px; }
    .section-title { font-size:18px; font-weight:700; margin-bottom:16px; color:#1f2937; }
    .muted { color:#6b7280; font-size:14px; }
    .badge { padding:6px 10px; border-radius:6px; font-weight:600; font-size:12px; display:inline-block; }
    .badge.blue { background:#dbeafe; color:#1e40af; }
    .badge.yellow { background:#fef3c7; color:#92400e; }
    .badge.green { background:#dcfce7; color:#166534; }
    .badge.red { background:#fee2e2; color:#991b1b; }
    table { width:100%; border-collapse:collapse; }
    thead { background:#f9fafb; }
    th, td { padding:14px 12px; border-bottom:1px solid #e5e7eb; text-align:left; font-size:14px; }
    th { font-weight:700; color:#374151; }
    tr:hover { background:#f9fafb; }
    .btn { display:inline-block; padding:8px 14px; border-radius:8px; background:#2563eb; color:#fff; text-decoration:none; font-weight:600; font-size:13px; text-decoration:none; }
    .btn.small { padding:6px 10px; font-size:12px; }
    .stats-row { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
    .stat-mini { background:#f9fafb; padding:12px 16px; border-radius:8px; flex:1; min-width:200px; }
    .stat-mini-label { font-size:12px; color:#6b7280; font-weight:600; }
    .stat-mini-value { font-size:24px; font-weight:800; color:#1f2937; margin-top:4px; }
    .empty-state { text-align:center; padding:40px 20px; color:#9ca3af; }
    .empty-state-icon { font-size:48px; margin-bottom:12px; }
    code { background:#f3f4f6; padding:4px 6px; border-radius:4px; font-size:13px; }
</style>

<div class="admin-wrap">

    {{-- Header --}}
    <div style="margin-bottom:28px;">
        <div class="admin-title">📊 Admin Dashboard - Laporan Harian</div>
        <div class="admin-subtitle">Tracking dan statistik laporan harian EXCO keseluruhan</div>
    </div>

    {{-- Main Statistics Cards --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">📑 Jumlah Total Laporan</div>
            <div class="stat-number">{{ $totalLaporans }}</div>
            <div class="stat-subtext">Semua laporan yang direkodkan</div>
        </div>

        <div class="stat-card draft">
            <div class="stat-label">✏️ Status Draf</div>
            <div class="stat-number">{{ $draftCount }}</div>
            <div class="stat-subtext">Laporan yang belum lengkap</div>
        </div>

        <div class="stat-card submitted">
            <div class="stat-label">✅ Status Dihantar</div>
            <div class="stat-number">{{ $submittedCount }}</div>
            <div class="stat-subtext">Laporan yang diluluskan</div>
        </div>

        <div class="stat-card resubmit">
            <div class="stat-label">⚠️ Perlu Hantar Semula</div>
            <div class="stat-number">{{ $resubmitCount }}</div>
            <div class="stat-subtext">Memerlukan penambahbaikan</div>
        </div>
    </div>

  {{-- search report by submition date --}}

  <form action="{{ route('homepage.admin') }}" method="GET" style="margin-bottom: 20px; background: #fff; padding: 16px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); display: flex; gap: 12px; align-items: flex-end;">
    <div style="flex: 1;">
        <label for="from_date" style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 13px; color: #374151;">From Date:</label>
        <input type="date" name="from_date" id="from_date" 
               value="{{ request('from_date') }}" 
               style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
    </div>

    <div style="flex: 1;">
        <label for="to_date" style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 13px; color: #374151;">To Date:</label>
        <input type="date" name="to_date" id="to_date" 
               value="{{ request('to_date') }}" 
               style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; font-size: 14px;">
    </div>

    <div style="width: 220px;">
        <label for="status" style="display:block; margin-bottom:4px; font-weight:600; font-size:13px; color:#374151;">Status:</label>
        <select name="status" id="status" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; font-size:14px;">
            <option value="all" {{ request('status','all') === 'all' ? 'selected' : '' }}>All Status</option>
            <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Draf</option>
            <option value="dihantar" {{ request('status') === 'dihantar' ? 'selected' : '' }}>Dihantar</option>
            <option value="disahkan" {{ request('status') === 'disahkan' ? 'selected' : '' }}>Disahkan</option>
            <option value="resubmit" {{ request('status') === 'resubmit' ? 'selected' : '' }}>Perlu Hantar Semula</option>
        </select>
    </div>

    <button type="submit" style="background: #2563eb; color: white; padding: 8px 16px; border-radius: 8px; border: none; font-weight: 600; font-size: 13px; cursor: pointer; white-space: nowrap;">
        🔍 Filter
    </button>
    
    <a href="{{ route('homepage.admin') }}" style="color: #6b7280; text-decoration: none; font-size: 13px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; display: inline-block; white-space: nowrap;">Clear</a>
</form>

    {{-- Recent Reports Section --}}
    <div class="chart-section">
        <div class="section-title">Laporan Terkini (50 Rekod Terakhir)</div>

        @if($recentLaporans->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <p>Tiada laporan terkini</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:15%;">Tarikh Laporan</th>
                            <th style="width:25%;">Nama EXCO</th>
                            <th style="width:15%;">Status</th>
                            <th style="width:15%;">Tarikh Hantar</th>
                            <th style="width:15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLaporans as $laporan)
                        <tr>
                            <td>
                                <strong>{{ $laporan->tarikh_laporan ?? $laporan->created_at->toDateString() }}</strong>
                            </td>
                            <td>
                                <strong>{{ $laporan->display_nama_exco ?? ($laporan->nama_exco ?? '-') }}</strong>

                            </td>
                            <td>
                                @php $status = $laporan->status_laporan; @endphp
                                @if($status === 'draf')
                                    <span class="badge yellow">Draf</span>
                                @elseif($status === 'disahkan')
                                    <span class="badge green">Disahkan</span>
                                @elseif($status === 'dihantar')
                                    <span class="badge green">Dihantar</span>
                                @elseif(in_array($status, ['hantar_semula','tolak','perlu_hantar_semula']))
                                    <span class="badge red">Hantar Semula</span>
                                @else
                                    <span class="badge blue">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($laporan->tarikh_hantar)
                                    {{ \Carbon\Carbon::parse($laporan->tarikh_hantar)->format('d/m/Y') }}
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('laporan.reviewAdmin', $laporan->id_laporan) }}" class="btn btn-small">Semak</a>
                                
                                <form action="{{ route('laporan.destroy', $laporan->id_laporan) }}" method="POST" style="display:inline-block;margin-left:8px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-small" style="background:#ef4444; padding:6px 10px; border-radius:8px;" onclick="return confirm('Padam laporan ini? Tindakan ini tidak boleh dibatalkan.')">Padam</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Quick Stats Footer --}}
    <div style="background:#f9fafb; padding:16px; border-radius:12px; margin-top:24px;">
        <div style="font-size:13px; color:#6b7280; line-height:1.8;">
            <strong>📈 Ringkasan Pantas:</strong><br>
            • Total laporan dalam sistem: <strong>{{ $totalLaporans }}</strong> laporan<br>
            • Peratus penghantaran: <strong>{{ $totalLaporans > 0 ? round(($submittedCount / $totalLaporans) * 100) : 0 }}%</strong><br>
            • Laporan memerlukan tindakan: <strong>{{ $resubmitCount }}</strong> ({{ $totalLaporans > 0 ? round(($resubmitCount / $totalLaporans) * 100) : 0 }}%)<br>
            • Laporan dalam draf: <strong>{{ $draftCount }}</strong> ({{ $totalLaporans > 0 ? round(($draftCount / $totalLaporans) * 100) : 0 }}%)
        </div>
    </div>

</div>
@endsection