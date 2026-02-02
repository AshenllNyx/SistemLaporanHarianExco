@extends('layouts.app')

@section('title', 'Senarai Kehadiran')

@section('content')

<style>
    /* Reusing Admin Dashboard Styles */
    .admin-wrap {
        width: 95%; max-width: 1200px; margin: 30px auto; font-family: 'Inter', sans-serif;
    }
    .admin-header { margin-bottom: 24px; }
    .admin-title { font-size: 26px; font-weight: 800; color: #1f2937; margin-bottom: 4px; }
    .admin-subtitle { font-size: 14px; color: #6b7280; }

    /* Card & Table */
    .card {
        background: #fff; border-radius: 16px; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); 
        border: 1px solid #f3f4f6; padding: 20px;
    }
    
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    td { padding: 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #374151; vertical-align: top; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f9fafb; }

    /* Badges & Text */
    .badge { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-red { background: #fee2e2; color: #991b1b; }
    .badge-yellow { background: #fef3c7; color: #92400e; }
    
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    
    .absent-list { margin: 0; padding-left: 20px; font-size: 13px; color: #ef4444; }
    .absent-list li { margin-bottom: 2px; }

    /* Buttons */
    .btn { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.2s; }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-danger { background: #fee2e2; color: #dc2626; }
    .btn-danger:hover { background: #fecaca; }

    /* Filter Form */
    .filter-bar { display: flex; gap: 10px; margin-bottom: 20px; align-items: end; background: #fff; padding: 16px; border-radius: 12px; border: 1px solid #f3f4f6; }
    .form-group label { display: block; font-size: 12px; font-weight: 600; color: #4b5563; margin-bottom: 4px; }
    .form-control { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
</style>

<div class="admin-wrap">
    
    <div class="admin-header">
        <h1 class="admin-title">📋 Bilangan Kehadiran Pelajar</h1>
        <p class="admin-subtitle">Rekod kehadiran harian mengikut dorm dan laporan EXCO</p>
    </div>

    {{-- Filter --}}
    <form action="{{ route('kehadiran.index') }}" method="GET" class="filter-bar">
        <div class="form-group">
            <label>Dari Tarikh</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
        <div class="form-group">
            <label>Hingga Tarikh</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 9px 16px;">Tapis</button>
        <a href="{{ route('kehadiran.index') }}" class="btn btn-danger" style="padding: 9px 16px; text-decoration: none; display: flex; align-items: center;">Reset</a>
    </form>

    <div class="card" style="overflow-x: auto;">
        @if(empty($attendanceData))
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <p>Tiada rekod kehadiran dijumpai.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Tarikh</th>
                        <th style="width: 20%;">Nama EXCO</th>
                        <th style="width: 15%;">Dorm</th>
                        <th style="width: 15%;" class="text-center">Kehadiran Keseluruhan</th>
                        <th style="width: 20%;">Pelajar Tidak Hadir</th>
                        <th style="width: 15%; text-align: right;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendanceData as $row)
                        <tr>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($row->tarikh)->format('d M Y') }}</div>
                                <div style="font-size: 12px; color: #9ca3af;">{{ \Carbon\Carbon::parse($row->tarikh)->format('l') }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $row->nama_exco }}</div>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: #374151;">{{ Str::limit($row->dorms, 30) }}</div>
                            </td>
                            <td class="text-center">
                                <div style="font-weight: 700; font-size: 16px;">
                                    {{ $row->present }} / {{ $row->total }}
                                </div>
                                <div style="font-size:11px; color:#6b7280; margin-top:2px;">Pelajar</div>
                            </td>
                            <td>
                                @if(count($row->absent_list) > 0)
                                    <ul class="absent-list">
                                        @foreach($row->absent_list as $student)
                                            <li>
                                                <span style="font-weight:600;">{{ $student['name'] }}</span> 
                                                <span style="color:#6b7280; font-size:11px;">({{ $student['dorm'] }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span style="color: #10b981; font-weight: 600; font-size: 13px;">✔ Semua Hadir</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    {{-- Sahkan --}}
                                    @if($row->status !== 'disahkan')
                                        <button type="button" class="btn btn-primary" onclick="openConfirmModal('{{ $row->id_laporan }}', {{ json_encode($row->absent_list) }})">Sahkan</button>
                                    @else
                                        <span class="badge badge-green">Disahkan</span>
                                    @endif

                                    {{-- Padam --}}
                                    <form action="{{ route('laporan.destroy', $row->id_laporan) }}" method="POST" onsubmit="return confirm('Padam laporan ini? Data tidak boleh dikembalikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Padam</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Confirmation Modal --}}
    <div id="confirmModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; width:90%; max-width:500px; padding:24px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0; font-size:18px; color:#1f2937;">Sahkan Laporan</h3>
            <p style="color:#6b7280; font-size:14px; margin-bottom:16px;">Sila semak senarai pelajar yang tidak hadir sebelum mengesahkan laporan ini.</p>
            
            <div style="background:#f9fafb; padding:16px; border-radius:8px; margin-bottom:20px; max-height:200px; overflow-y:auto;">
                <h4 style="font-size:13px; text-transform:uppercase; color:#9ca3af; margin-top:0; margin-bottom:8px;">Pelajar Tidak Hadir:</h4>
                <ul id="modalAbsentList" class="absent-list" style="margin:0; padding-left:20px;">
                    {{-- Dynamically populated --}}
                </ul>
                <p id="modalNoAbsent" style="display:none; color:#10b981; font-size:13px; font-weight:600; margin:0;">✔ Semua Pelajar Hadir</p>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn" style="background:#f3f4f6; color:#374151;" onclick="closeConfirmModal()">Batal</button>
                <form id="confirmForm" action="" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Sahkan Laporan</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function openConfirmModal(id, absentList) {
        // Set Form Action
        const form = document.getElementById('confirmForm');
        form.action = "/laporan/" + id + "/pengesahan"; // Matches Route::post('/laporan/{laporan}/pengesahan')

        // Populate List
        const listEl = document.getElementById('modalAbsentList');
        const noAbsentEl = document.getElementById('modalNoAbsent');
        listEl.innerHTML = '';
        
        if (absentList.length > 0) {
            noAbsentEl.style.display = 'none';
            absentList.forEach(student => {
                const li = document.createElement('li');
                li.innerHTML = `<span style="font-weight:600; color:#374151;">${student.name}</span> <span style="color:#9ca3af; font-size:12px;">(${student.dorm})</span>`;
                listEl.appendChild(li);
            });
        } else {
            noAbsentEl.style.display = 'block';
        }

        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    // Close on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('confirmModal');
        if (event.target == modal) {
            closeConfirmModal();
        }
    }
</script>

@endsection
