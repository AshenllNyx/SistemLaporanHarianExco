@extends('layouts.app')

@section('title','Senarai Dorm')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h2 style="font-size:22px;font-weight:700;margin:0">Senarai Dorm</h2>
    <a href="{{ route('dorms.create') }}" class="btn" style="padding:10px 14px;border-radius:8px;background:#2563eb;color:white;text-decoration:none">+ Tambah Dorm</a>
</div>

@if(session('success'))
    <div style="background:#ecfdf3;border:1px solid #bbf7d0;padding:12px;border-radius:8px;color:#166534;margin-bottom:14px">
        {{ session('success') }}
    </div>
@endif

<div style="background:white;padding:16px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="text-align:left">
                    <th style="padding:10px;border-bottom:1px solid #eef2ff">Nama Dorm</th>
                    <th style="padding:10px;border-bottom:1px solid #eef2ff">Blok</th>
                    <th style="padding:10px;border-bottom:1px solid #eef2ff">Kapasiti</th>
                    <th style="padding:10px;border-bottom:1px solid #eef2ff">Bil. Pelajar</th>
                    <th style="padding:10px;border-bottom:1px solid #eef2ff;text-align:right">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dorms as $dorm)
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #f3f4f6">{{ $dorm->nama_dorm }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f3f4f6">{{ $dorm->blok }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f3f4f6">{{ $dorm->capacity ?? '-' }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f3f4f6">{{ $dorm->pelajars_count ?? $dorm->pelajars->count() ?? 0 }}</td>
                        <td style="padding:10px;border-bottom:1px solid #f3f4f6;text-align:right">
                            <a href="{{ route('dorms.show', $dorm->id_dorm) }}" 
                               style="display:inline-block;padding:6px 10px;border-radius:6px;background:#3b82f6;color:white;text-decoration:none;font-size:13px;font-weight:600;margin-right:6px">
                                Lihat
                            </a>
                            <a href="{{ route('dorms.edit', $dorm->id_dorm) }}" 
                               style="display:inline-block;padding:6px 10px;border-radius:6px;background:#f59e0b;color:white;text-decoration:none;font-size:13px;font-weight:600;margin-right:6px">
                                Edit
                            </a>
                           <form action="{{ route('dorms.destroy', $dorm->id_dorm) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Padam</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:12px;text-align:center;color:#6b7280">Tiada dorm direkodkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

