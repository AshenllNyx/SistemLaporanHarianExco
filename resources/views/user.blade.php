@extends('layouts.app')

@section('title', 'Senarai Pengguna')

@section('content')
<style>
    .page-title {
        font-size: 22px;
        font-weight: 700;
        color:#0f172a;
        margin-bottom: 16px;
    }

    .filter-box {
        background: white;
        padding: 16px;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(2,6,23,0.08);
        border: 1px solid rgba(15,23,42,0.04);
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
    }

    .filter-button {
        padding: 10px 16px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-button.active {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    .filter-button:hover {
        background: #dbeafe;
    }

    .btn-search {
        padding: 10px 20px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-search:hover {
        background: #1d4ed8;
    }

    .table-box {
        background:white;
        padding:20px;
        border-radius:14px;
        box-shadow:0 10px 30px rgba(2,6,23,0.08);
        border:1px solid rgba(15,23,42,0.04);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top:10px;
        border-radius: 10px;
        overflow: hidden;
    }

    th {
        background: #2563eb;
        color:white;
        padding:14px;
        text-align:left;
        font-size:14px;
    }
    td {
        padding:12px 14px;
        border-bottom:1px solid #e2e8f0;
        font-size:14px;
        color:#0f172a;
    }
    tr:hover{
        background:#eef2ff;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-approved {
        background: #dcfce7;
        color: #166534;
    }

    .status-pending {
        background: #fef08a;
        color: #854d0e;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-small {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-approve {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .btn-approve:hover {
        background: #bbf7d0;
    }

    .btn-edit {
        background: #dbeafe;
        color: #0c4a6e;
        border: 1px solid #7dd3fc;
    }

    .btn-edit:hover {
        background: #bfdbfe;
    }

    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .btn-delete:hover {
        background: #fecaca;
    }

    .btn-search-clear {
        padding: 10px 16px;
        background: #f3f4f6;
        color: #0f172a;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-search-clear:hover {
        background: #e5e7eb;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
</style>

<h2 class="page-title">Senarai Pengguna</h2>

<!-- Search and Filter Box -->
<div class="filter-box">
    <form action="{{ route('users.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" class="search-input" placeholder="Cari nama, email, atau username..." value="{{ $search ?? '' }}">
        
        <select name="status" class="filter-button">
            <option value="">Semua Status</option>
            <option value="not-approved" {{ ($status ?? '') === 'not-approved' ? 'selected' : '' }}>Tidak Disahkan</option>
            <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Disahkan</option>
        </select>

        <button type="submit" class="btn-search">🔍 Filter</button>
        
        @if($search || $status)
        <a href="{{ route('users.index') }}" class="btn-search-clear">Clear</a>
        @endif
    </form>
</div>

<div class="table-box">
@if($users->count() > 0)
<table>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Username</th>
        <th>Email</th>
        <th>Jantina</th>
        <th>Tahap</th>
        <th>Status</th>
        <th>Tindakan</th>
    </tr>

    @foreach($users as $index => $user)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->user_name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->jantina }}</td>
        <td>{{ $user->level == 'admin' ? 'Admin' : 'Pengguna Biasa' }}</td>
        <td>
            @if($user->is_approved)
                <span class="status-badge status-approved">✓ Disahkan</span>
            @else
                <span class="status-badge status-pending">✗ Tidak Disahkan</span>
            @endif
        </td>
        <td>
            <div class="action-buttons">
                @if(!$user->is_approved)
                <form action="{{ route('users.approve', $user->no_ic) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-small btn-approve" onclick="return confirm('Sahkan pengguna ini?')">Sahkan</button>
                </form>
                @endif
                <a href="{{ route('users.edit', $user->no_ic) }}" class="btn-small btn-edit">Edit</a>
                <form action="{{ route('users.destroy', $user->no_ic) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-small btn-delete" onclick="return confirm('Adakah anda pasti? Tindakan ini tidak dapat dibatalkan.')">Hapus</button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach
</table>
@else
<div class="empty-state">
    <p>Tiada pengguna ditemukan</p>
</div>
@endif
</div>

@endsection
