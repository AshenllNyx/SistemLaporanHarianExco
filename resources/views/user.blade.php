@extends('layouts.app')

@section('title', 'Senarai Pengguna')

@section('content')
<style>
    .wrap { width: 95%; max-width:1200px; margin: 24px auto; }
    .title { font-size:24px; font-weight:800; margin-bottom:20px; }
    .filter-box {
        background: white;
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    .filter-select {
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
    }
    .btn-search {
        padding: 10px 16px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }
    .btn-search:hover { background: #1d4ed8; }
    .btn-clear {
        padding: 10px 16px;
        background: #f3f4f6;
        color: #111;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-clear:hover { background: #e5e7eb; }
    .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:20px; }
    .user-card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); border-left:5px solid #3b82f6; transition:transform 0.2s, box-shadow 0.2s; }
    .user-card:hover { transform:translateY(-4px); box-shadow:0 6px 16px rgba(0,0,0,0.12); }
    .user-header { display:flex; justify-content:space-between; align-items:start; margin-bottom:12px; }
    .user-name { font-size:18px; font-weight:700; margin:0; color:#111; }
    .user-level { font-size:12px; font-weight:600; padding:4px 8px; border-radius:6px; background:#eff6ff; color:#1e40af; }
    .user-info { font-size:14px; color:#6b7280; margin:8px 0; display:flex; align-items:center; gap:6px; }
    .user-info-label { font-weight:600; min-width:70px; }
    .status-badge { display:inline-block; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; }
    .status-approved { background:#d1fae5; color:#065f46; }
    .status-pending { background:#fef3c7; color:#92400e; }
    .action-buttons { display:flex; gap:8px; margin-top:14px; flex-wrap:wrap; }
    .btn-action { padding:8px 12px; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; flex:1; text-align:center; min-width:80px; }
    .btn-approve { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    .btn-approve:hover { background:#a7f3d0; }
    .btn-edit { background:#dbeafe; color:#0c4a6e; border:1px solid #7dd3fc; }
    .btn-edit:hover { background:#bfdbfe; }
    .btn-delete { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
    .btn-delete:hover { background:#fecaca; }
    .empty-state { text-align:center; padding:60px 20px; color:#6b7280; }
    .empty-icon { font-size:48px; margin-bottom:16px; }
    .divider { height:1px; background:#f3f4f6; margin:12px 0; }
</style>

<div class="wrap">
    <h1 class="title">Senarai Pengguna</h1>

    <!-- Filter Box -->
    <div class="filter-box">
        <form action="{{ route('users.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" class="search-input" placeholder="Cari nama, email, atau username..." value="{{ $search ?? '' }}">
            
            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                <option value="not-approved" {{ ($status ?? '') === 'not-approved' ? 'selected' : '' }}>Tidak Disahkan</option>
                <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Disahkan</option>
            </select>

            <button type="submit" class="btn-search">🔍 Filter</button>
            
            @if($search || $status)
                <a href="{{ route('users.index') }}" class="btn-clear">Clear</a>
            @endif
        </form>
    </div>

    <!-- Users Grid -->
    @if($users->count() > 0)
        <div class="grid">
            @foreach($users as $user)
                <div class="user-card">
                    <div class="user-header">
                        <h2 class="user-name">{{ $user->name }}</h2>
                        <span class="user-level">{{ $user->level == 'admin' ? '👑 Admin' : '👤 User' }}</span>
                    </div>

                    <div class="user-info">
                        <span class="user-info-label">Username:</span>
                        <span>{{ $user->user_name }}</span>
                    </div>

                    <div class="user-info">
                        <span class="user-info-label">Email:</span>
                        <span style="word-break:break-all;">{{ $user->email }}</span>
                    </div>

                    <div class="user-info">
                        <span class="user-info-label">Jantina:</span>
                        <span>
                            @if($user->jantina === 'L' || $user->jantina === 'Lelaki')
                                🧑 Lelaki
                            @elseif($user->jantina === 'P' || $user->jantina === 'Perempuan')
                                👩 Perempuan
                            @else
                                {{ $user->jantina }}
                            @endif
                        </span>
                    </div>

                    <div class="divider"></div>

                    <div class="user-info">
                        <span class="user-info-label">Status:</span>
                        @if($user->is_approved)
                            <span class="status-badge status-approved">✓ Disahkan</span>
                        @else
                            <span class="status-badge status-pending">✗ Belum Disahkan</span>
                        @endif
                    </div>

                    <div class="action-buttons">
                        @if(!$user->is_approved)
                            <form action="{{ route('users.approve', $user->no_ic) }}" method="POST" style="flex:1;">
                                @csrf
                                <button type="submit" class="btn-action btn-approve" onclick="return confirm('Sahkan pengguna ini?')">Sahkan</button>
                            </form>
                        @endif
                        <a href="{{ route('users.edit', $user->no_ic) }}" class="btn-action btn-edit" style="text-decoration:none;">Edit</a>
                        <form action="{{ route('users.destroy', $user->no_ic) }}" method="POST" style="flex:1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete" onclick="return confirm('Adakah anda pasti? Tindakan ini tidak dapat dibatalkan.')">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">👥</div>
            <p style="font-size:18px; margin:0">Tiada pengguna ditemukan</p>
        </div>
    @endif
</div>
@endsection
