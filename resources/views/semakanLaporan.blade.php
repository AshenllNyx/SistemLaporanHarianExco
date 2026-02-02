@extends('layouts.app')

@section('title', 'Semakan Laporan')

@section('content')
    @php
        // Fallback checks
        $recentLaporans = $recentLaporans ?? collect();
    @endphp

    <style>
        /* Admin Dashboard Shared Styles */
        .admin-wrap {
            width: 95%;
            max-width: 1200px;
            margin: 24px auto;
            font-family: 'Inter', sans-serif;
        }

        .admin-header {
            margin-bottom: 24px;
        }

        .admin-title {
            font-size: 28px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .admin-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        /* Card Styles */
        .card {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            border: 1px solid #f3f4f6;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        /* Form Styles */
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #1f2937;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }

        .btn-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #f3f4f6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        thead {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.05em;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f9fafb;
        }

        /* Status indicator left borders */
        tr.status-draf { border-left: 4px solid #f59e0b; }
        tr.status-dihantar { border-left: 4px solid #10b981; }
        tr.status-disahkan { border-left: 4px solid #3b82f6; }
        tr.status-resubmit { border-left: 4px solid #ef4444; }

        tr:hover td {
            background: #f8fafc;
            color: #2563eb;
        }

        .badge {
            transition: all 0.2s;
            cursor: default;
        }

        .badge:hover {
            transform: scale(1.05);
            filter: brightness(1.05);
        }

        /* Better Badges */
        .badge-yellow { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-green { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-red { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-blue { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
    </style>

    <div class="admin-wrap">
        {{-- Header --}}
        <div class="admin-header">
            <h1 class="admin-title">📄 Semakan Laporan</h1>
            <p class="admin-subtitle">Tapis dan urus laporan harian EXCO</p>
        </div>

        {{-- Filter Section --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">🔍 Filter Carian</h2>
            </div>
            
            <form action="{{ route('semakan.Laporan') }}" method="GET">
                <div class="filter-grid">
                    <div class="form-group">
                        <label for="from_date">Dari Tarikh</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>

                    <div class="form-group">
                        <label for="to_date">Hingga Tarikh</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    <div class="form-group">
                        <label for="status">Status Laporan</label>
                        <select name="status" id="status" class="form-control">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Draf</option>
                            <option value="dihantar" {{ request('status') === 'dihantar' ? 'selected' : '' }}>Dihantar</option>
                            <option value="disahkan" {{ request('status') === 'disahkan' ? 'selected' : '' }}>Disahkan</option>
                            <option value="resubmit" {{ request('status') === 'resubmit' ? 'selected' : '' }}>Perlu Hantar Semula</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            Tapis
                        </button>
                        <a href="{{ route('semakan.Laporan') }}" class="btn btn-outline">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Results Section --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Senarai Laporan ({{ $recentLaporans->count() }} Rekod)</h2>
            </div>

            @if($recentLaporans->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">�</div>
                    <h3 style="font-weight: 600; color: #374151;">Tiada laporan ditemui</h3>
                    <p style="color: #6b7280; font-size: 14px;">Cuba ubah tetapan filter anda.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tarikh Laporan</th>
                                <th>Nama EXCO</th>
                                <th>Status</th>
                                <th>Tarikh Hantar</th>
                                <th style="text-align: right;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLaporans as $laporan)
                                @php 
                                    $status = $laporan->status_laporan;
                                    $rowClass = 'status-' . $status;
                                    if (in_array($status, ['hantar_semula','tolak','perlu_hantar_semula'])) $rowClass = 'status-resubmit';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <div style="font-weight: 600;">
                                            {{ \Carbon\Carbon::parse($laporan->tarikh_laporan ?? $laporan->created_at)->format('d M Y') }}
                                        </div>
                                        <div style="font-size: 12px; color: #9ca3af;">
                                            {{ \Carbon\Carbon::parse($laporan->tarikh_laporan ?? $laporan->created_at)->format('l') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500;">{{ $laporan->display_nama_exco ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @php $status = $laporan->status_laporan; @endphp
                                        @if($status === 'draf')
                                            <span class="badge badge-yellow">Draf</span>
                                        @elseif($status === 'disahkan')
                                            <span class="badge badge-green">Disahkan</span>
                                        @elseif($status === 'dihantar')
                                            <span class="badge badge-green">Dihantar</span>
                                        @elseif(in_array($status, ['hantar_semula','tolak','perlu_hantar_semula']))
                                            <span class="badge badge-red">Hantar Semula</span>
                                        @else
                                            <span class="badge badge-blue">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($laporan->tarikh_hantar)
                                            <div>{{ \Carbon\Carbon::parse($laporan->tarikh_hantar)->format('d/m/Y') }}</div>
                                            <div style="font-size: 11px; color: #9ca3af;">{{ \Carbon\Carbon::parse($laporan->tarikh_hantar)->format('h:i A') }}</div>
                                        @else
                                            <span style="color: #d1d5db;">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 8px; justify-content: flex-end;">
                                            @if($status === 'disahkan')
                                                <a href="{{ route('laporan.reviewAdmin', $laporan->id_laporan) }}" class="btn btn-outline btn-sm">
                                                    🖨️ Cetak
                                                </a>
                                            @else
                                                <a href="{{ route('laporan.reviewAdmin', $laporan->id_laporan) }}" class="btn btn-primary btn-sm">
                                                    🔍 Semak
                                                </a>
                                            @endif

                                            <form action="{{ route('laporan.destroy', $laporan->id_laporan) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam laporan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection