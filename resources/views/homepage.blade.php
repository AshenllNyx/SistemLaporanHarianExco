@extends('layouts.app')

@section('content')

@php
    // fallback (elak Undefined variable)
    $laporans = $laporans ?? collect();
    $laporanHantarSemula = $laporanHantarSemula ?? collect();
@endphp

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    .dashboard-container {
        width: 100%;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 30px 20px;
    }

    .dashboard-content {
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-header {
        margin-bottom: 40px;
    }

    .dashboard-title {
        font-size: 32px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .dashboard-subtitle {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border-top: 4px solid #3b82f6;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .stat-card.accent-red { border-top-color: #ef4444; }
    .stat-card.accent-yellow { border-top-color: #f59e0b; }
    .stat-card.accent-green { border-top-color: #10b981; }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1f2937;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 32px 0 16px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    .section-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Report Cards Grid */
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 30px;
    }

    .report-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #3b82f6;
        transition: all 0.3s ease;
    }

    .report-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .report-card.alert { border-left-color: #ef4444; background: #fef2f2; }
    
    .report-date {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .report-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 13px;
    }

    .report-status {
        padding: 6px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }

    .status-draf { background: #fef3c7; color: #92400e; }
    .status-dihantar { background: #d1fae5; color: #065f46; }
    .status-disahkan { background: #d1fae5; color: #065f46; }
    .status-hantar-semula { background: #fee2e2; color: #991b1b; }

    .report-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .report-actions form {
        display: flex;
        flex: 1;
    }

    .btn {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 6px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-align: center;
        flex: 1;
    }

    .btn:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #64748b;
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-radius: 12px;
        color: #6b7280;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .empty-state-text {
        font-size: 16px;
        margin-bottom: 20px;
    }

    /* Table styles for list view */
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    th {
        padding: 14px;
        text-align: left;
        font-weight: 700;
        color: #374151;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 14px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        color: #4b5563;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    .muted {
        color: #6b7280;
        font-size: 13px;
    }

    /* Action Buttons */
    .table-actions {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .table-actions form {
        display: flex;
    }

    .table-actions .btn {
        padding: 6px 10px;
        font-size: 12px;
        flex: unset;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .reports-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-title {
            font-size: 24px;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 10px;
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-content">
        
        {{-- Header --}}
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div style="flex: 1; min-width: 280px;">
                <h1 class="dashboard-title">📋 Laporan Harian Exco Aspura KVDSAZI</h1>
                <p class="dashboard-subtitle">Kelola dan pantau laporan harian anda dengan mudah</p>
            </div>
            
            <div class="attendance-box" style="background: white; padding: 16px 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 5px solid #10b981; display: flex; flex-direction: column; align-items: flex-start; min-width: 200px;">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">KEHADIRAN HARI INI</div>
                <div style="font-size: 12px; font-weight: 600; color: #94a3b8; margin-bottom: 6px;">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1;">
                    {{ $todayPresent }} <span style="font-size: 16px; color: #94a3b8; font-weight: 600;">/ {{ $totalStudents }}</span>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Jumlah Laporan</div>
                <div class="stat-value">{{ $laporans->count() }}</div>
            </div>

            <div class="stat-card accent-yellow">
                <div class="stat-label">Dalam Draf</div>
                <div class="stat-value">{{ $laporans->where('status_laporan','draf')->count() }}</div>
            </div>

            <div class="stat-card accent-green">
                <div class="stat-label">Dihantar</div>
                <div class="stat-value">{{ $laporans->where('status_laporan','dihantar')->count() }}</div>
            </div>

            <div class="stat-card accent-red">
                <div class="stat-label">Perlu Tindakan</div>
                <div class="stat-value">{{ $laporanHantarSemula->count() }}</div>
            </div>
        </div>

        {{-- Alert Reports Section --}}
        @if(!$laporanHantarSemula->isEmpty())
        <div class="section-header">
            <h2 class="section-title">⚠️ Laporan Perlu Tindakan</h2>
            <span class="section-badge">{{ $laporanHantarSemula->count() }} item</span>
        </div>

        <div class="reports-grid">
            @foreach($laporanHantarSemula as $lap)
                <div class="report-card alert">
                    <div class="report-date">{{ \Carbon\Carbon::parse($lap->tarikh_laporan)->format('d/m/Y') }}</div>
                    
                    <div class="report-meta">
                        <span class="report-status status-hantar-semula">⚠️ Perlu Tindakan</span>
                        @if($lap->tarikh_hantar)
                            <span class="muted">🕒 {{ \Carbon\Carbon::parse($lap->tarikh_hantar)->format('h:i A') }}</span>
                        @endif
                    </div>

                    @if($lap->butiranLaporans && $lap->butiranLaporans->count())
                        <div class="muted" style="margin-bottom: 12px; line-height: 1.5;">
                            @foreach($lap->butiranLaporans->take(2) as $b)
                                <div style="margin-bottom: 6px;">
                                    @php
                                        $data = $b->data_tambahan ?? [];
                                        $dormName = $b->dorm->nama_dorm ?? ($b->id_dorm ? 'Dorm #'.$b->id_dorm : '-');
                                    @endphp
                                    @if($b->jenis_butiran === 'dorm')
                                        <strong>Dorm {{ $dormName }}</strong>
                                    @elseif($b->jenis_butiran === 'disiplin')
                                        <strong>Isu Disiplin</strong>
                                    @elseif($b->jenis_butiran === 'kerosakan')
                                        <strong>Kerosakan</strong>
                                    @else
                                        <strong>{{ ucfirst($b->jenis_butiran) }}</strong>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="report-actions">
                        <a href="{{ route('laporan.review', $lap->id_laporan) }}" class="btn">Lihat</a>
                        <a href="{{ route('laporan.edit', $lap->id_laporan) }}" class="btn btn-secondary">Kemaskini</a>
                        <form action="{{ route('laporan.destroy', $lap->id_laporan) }}" method="POST" onsubmit="return confirm('Padam laporan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="width: 100%;">Padam</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- All Reports Section --}}
        <div class="section-header">
            <h2 class="section-title">📊 Senarai Laporan</h2>
            <span class="section-badge">{{ $laporans->count() }} laporan</span>
        </div>

        @if($laporans->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">Belum ada laporan</div>
                <a href="{{ route('laporan.create') }}" class="btn" style="display: inline-block; margin-top: 16px;">+ Buat Laporan Baru</a>
            </div>
        @else
            {{-- Card View --}}
            <div class="reports-grid">
                @foreach($laporans as $lap)
                    <div class="report-card">
                        <div class="report-date">{{ \Carbon\Carbon::parse($lap->tarikh_laporan)->format('d/m/Y') }}</div>
                        
                        <div class="report-meta">
                            @php $s = $lap->status_laporan; @endphp
                            @if($s == 'dihantar')
                                <span class="report-status status-dihantar">✓ Dihantar</span>
                            @elseif($s == 'disahkan')
                                <span class="report-status status-disahkan">✓ Disahkan</span>
                            @elseif(in_array($s,['hantar_semula','perlu_hantar_semula','tolak']))
                                <span class="report-status status-hantar-semula">⚠️ Hantar Semula</span>
                            @elseif($s == 'draf')
                                <span class="report-status status-draf">○ Draf</span>
                            @else
                                <span class="report-status">{{ $s }}</span>
                            @endif

                            @if($lap->tarikh_hantar)
                                <span class="muted">🕒 {{ \Carbon\Carbon::parse($lap->tarikh_hantar)->format('h:i A') }}</span>
                            @endif
                        </div>

                        @if($lap->butiranLaporans && $lap->butiranLaporans->count())
                            <div class="muted" style="margin: 12px 0; line-height: 1.6; font-size: 13px;">
                                @foreach($lap->butiranLaporans->take(2) as $b)
                                    <div style="margin-bottom: 6px;">
                                        @php
                                            $data = $b->data_tambahan ?? [];
                                            $dormName = $b->dorm->nama_dorm ?? ($b->id_dorm ? 'Dorm #'.$b->id_dorm : '-');
                                        @endphp
                                        @if($b->jenis_butiran === 'dorm')
                                            📍 <strong>{{ $dormName }}</strong> - Kebersihan: {{ $data['kategori_kebersihan'] ?? '-' }}
                                        @elseif($b->jenis_butiran === 'disiplin')
                                            ⚡ <strong>Disiplin</strong> - {{ $data['jenis_kesalahan'] ?? $b->deskripsi_isu ?? '-' }}
                                        @elseif($b->jenis_butiran === 'kerosakan')
                                            🔧 <strong>Kerosakan</strong> - {{ $data['jenis_kerosakan'] ?? $b->deskripsi_isu ?? '-' }}
                                        @elseif($b->jenis_butiran === 'pelajar_sakit')
                                            🏥 <strong>Pelajar Sakit</strong> - {{ $data['jenis_sakit'] ?? $b->deskripsi_isu ?? '-' }}
                                        @elseif($b->jenis_butiran === 'dewan_makan')
                                            🍽️ <strong>Dewan Makan</strong> - {{ $data['jenis_isu'] ?? $b->deskripsi_isu ?? '-' }}
                                        @else
                                            {{ ucfirst($b->jenis_butiran) }}
                                        @endif
                                    </div>
                                @endforeach
                                @if($lap->butiranLaporans->count() > 2)
                                    <div style="margin-top: 6px; color: #3b82f6;">+{{ $lap->butiranLaporans->count() - 2 }} lagi</div>
                                @endif
                            </div>
                        @else
                            <div class="muted" style="margin: 12px 0;">Tiada butiran</div>
                        @endif

                        <div class="report-actions">
                            <a href="{{ route('laporan.review', $lap->id_laporan) }}" class="btn">Lihat</a>
                            @if($lap->status_laporan === 'draf' || $lap->status_laporan === 'dihantar' || in_array($lap->status_laporan, ['hantar_semula', 'tolak', 'perlu_hantar_semula']))
                                <a href="{{ route('laporan.edit', $lap->id_laporan) }}" class="btn btn-secondary">Kemaskini</a>
                            @endif
                            @if($lap->status_laporan === 'draf' || in_array($lap->status_laporan, ['hantar_semula', 'tolak', 'perlu_hantar_semula']))
                                <form action="{{ route('laporan.destroy', $lap->id_laporan) }}" method="POST" onsubmit="return confirm('Padam laporan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="width: 100%;">Padam</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Table View --}}
            <div style="margin-top: 40px;">
                <h3 style="font-size: 14px; font-weight: 700; color: #6b7280; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">📇 Tampilan Jadual</h3>
                <div class="table-responsive">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tarikh</th>
                                    <th>Status</th>
                                    <th>Butiran</th>
                                    <th style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laporans as $lap)
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($lap->tarikh_laporan)->format('d/m/Y') }}</strong>
                                        @if($lap->tarikh_hantar)
                                            <div class="muted" style="font-size: 11px;">🕒 {{ \Carbon\Carbon::parse($lap->tarikh_hantar)->format('h:i A') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @php $s = $lap->status_laporan; @endphp
                                        @if($s == 'dihantar')
                                            <span class="report-status status-dihantar">✓ Dihantar</span>
                                        @elseif($s == 'disahkan')
                                            <span class="report-status status-disahkan">✓ Disahkan</span>
                                        @elseif(in_array($s,['hantar_semula','perlu_hantar_semula','tolak']))
                                            <span class="report-status status-hantar-semula">⚠️ Hantar Semula</span>
                                        @elseif($s == 'draf')
                                            <span class="report-status status-draf">○ Draf</span>
                                        @else
                                            <span class="report-status">{{ $s }}</span>
                                        @endif
                                    </td>
                                    <td style="max-width: 400px; word-break: break-word;">
                                        @if($lap->butiranLaporans && $lap->butiranLaporans->count())
                                            @php $count = 0; @endphp
                                            @foreach($lap->butiranLaporans as $b)
                                                @if($count < 2)
                                                    <div style="font-size: 13px; margin-bottom: 4px;">
                                                        @php
                                                            $data = $b->data_tambahan ?? [];
                                                            $dormName = $b->dorm->nama_dorm ?? ($b->id_dorm ? 'Dorm #'.$b->id_dorm : '-');
                                                        @endphp
                                                        @if($b->jenis_butiran === 'dorm')
                                                            <strong>{{ $dormName }}:</strong> {{ $data['kategori_kebersihan'] ?? 'N/A' }}
                                                        @elseif($b->jenis_butiran === 'disiplin')
                                                            <strong>Disiplin:</strong> {{ $data['jenis_kesalahan'] ?? $b->deskripsi_isu ?? '-' }}
                                                        @elseif($b->jenis_butiran === 'kerosakan')
                                                            <strong>Kerosakan:</strong> {{ $data['jenis_kerosakan'] ?? $b->deskripsi_isu ?? '-' }}
                                                        @else
                                                            <strong>{{ ucfirst($b->jenis_butiran) }}:</strong> {{ $b->deskripsi_isu ?? 'N/A' }}
                                                        @endif
                                                    </div>
                                                    @php $count++; @endphp
                                                @endif
                                            @endforeach
                                            @if($lap->butiranLaporans->count() > 2)
                                                <div class="muted">+{{ $lap->butiranLaporans->count() - 2 }} lagi</div>
                                            @endif
                                        @else
                                            <span class="muted">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-actions">
                                            <a href="{{ route('laporan.review', $lap->id_laporan) }}" class="btn">Lihat</a>
                                            @if($lap->status_laporan === 'draf' || $lap->status_laporan === 'dihantar' || in_array($lap->status_laporan, ['hantar_semula', 'tolak', 'perlu_hantar_semula']))
                                                <a href="{{ route('laporan.edit', $lap->id_laporan) }}" class="btn btn-secondary">Kemaskini</a>
                                            @endif
                                            @if($lap->status_laporan === 'draf' || in_array($lap->status_laporan, ['hantar_semula', 'tolak', 'perlu_hantar_semula']))
                                                <form action="{{ route('laporan.destroy', $lap->id_laporan) }}" method="POST" onsubmit="return confirm('Padam laporan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Padam</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
