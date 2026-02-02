@extends('layouts.app')

@section('title', 'Butiran Dorm - ' . $dorm->nama_dorm)

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <div>
        <h2 style="font-size:22px;font-weight:700;margin:0 0 4px 0">{{ $dorm->nama_dorm }}</h2>
        <p style="margin:0;color:#6b7280;font-size:14px">Blok {{ $dorm->blok }} | Kapasiti: {{ $dorm->capacity ?? '-' }}</p>
    </div>
    <div style="display:flex;gap:8px">
        @if(Auth::user()->level == 'admin')
        <a href="{{ route('dorms.edit', $dorm->id_dorm) }}" 
           style="padding:10px 14px;border-radius:8px;background:#f59e0b;color:white;text-decoration:none;font-weight:600">
            Edit
        </a>
        @endif
        <a href="{{ Auth::user()->level == 'admin' ? route('dorms.index') : route('dorms.userlist') }}" 
           style="padding:10px 14px;border-radius:8px;background:#e5e7eb;color:#111;text-decoration:none;font-weight:600">
            Kembali
        </a>
    </div>
</div>

@if(Auth::user()->level == 'admin')
<div style="background:#f0f9ff;padding:16px;border-radius:8px;border:1px solid #bfdbfe;margin-top:20px">
    <h3 style="margin:0 0 12px 0;font-size:16px;font-weight:700">Tambah Pelajar Baru</h3>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px">
        <input type="text" id="newIc" placeholder="No. IC (cth: 123456789012)" 
               style="padding:10px;border:1px solid #60a5fa;border-radius:8px;font-size:14px;box-sizing:border-box">
        <input type="text" id="newName" placeholder="Nama Lengkap" 
               style="padding:10px;border:1px solid #60a5fa;border-radius:8px;font-size:14px;box-sizing:border-box">
        <select id="newJantina" style="padding:10px;border:1px solid #60a5fa;border-radius:8px;font-size:14px;box-sizing:border-box">
            <option value="">-- Pilih Jantina --</option>
            <option value="L">Lelaki</option>
            <option value="P">Perempuan</option>
        </select>
        <button type="button" onclick="addMember()" 
                style="padding:10px 16px;background:#f59e0b;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600">
            Tambah
        </button>
    </div>
</div>
@endif

<div style="background:white;padding:20px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06);margin-bottom:20px">
    <h3 style="margin:0 0 16px 0;font-size:18px;font-weight:700">Senarai Anggota Dorm</h3>
    
    @php
        $pelajar = $dorm->pelajars ?? collect([]);
    @endphp

    @if($pelajar->count() > 0)
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#f9fafb;text-align:left">
                        <th style="padding:12px;border-bottom:2px solid #e5e7eb;font-weight:700;font-size:14px">No. IC</th>
                        <th style="padding:12px;border-bottom:2px solid #e5e7eb;font-weight:700;font-size:14px">Nama</th>
                        <th style="padding:12px;border-bottom:2px solid #e5e7eb;font-weight:700;font-size:14px">Jantina</th>
                    </tr>
                </thead>
                <tbody id="pelajarList">
                    @foreach($pelajar as $p)
                        <tr>
                            <td style="padding:12px;border-bottom:1px solid #f3f4f6">{{ $p->no_ic }}</td>
                            <td style="padding:12px;border-bottom:1px solid #f3f4f6">{{ $p->nama }}</td>
                            <td style="padding:12px;border-bottom:1px solid #f3f4f6">
                                @if($p->jantina === 'L')
                                    <span style="background:#dbeafe;color:#1e40af;padding:4px 8px;border-radius:6px;font-size:13px;font-weight:600">Lelaki</span>
                                @elseif($p->jantina === 'P')
                                    <span style="background:#fce7f3;color:#be185d;padding:4px 8px;border-radius:6px;font-size:13px;font-weight:600">Perempuan</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p style="margin-top:16px;color:#6b7280;font-size:14px">
            <strong>Jumlah anggota:</strong> <span id="pelajarCount">{{ $pelajar->count() }}</span>
        </p>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px">
            <div style="background:white;padding:16px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
                <p style="margin:0 0 8px 0;color:#6b7280;font-size:13px;font-weight:600">KAPASITI</p>
                <p style="margin:0;font-size:20px;font-weight:800">{{ $dorm->capacity ?? '-' }}</p>
            </div>
            <div style="background:white;padding:16px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
                <p style="margin:0 0 8px 0;color:#6b7280;font-size:13px;font-weight:600">BILANGAN ANGGOTA</p>
                <p style="margin:0;font-size:20px;font-weight:800" id="bilanganAnggota">{{ $pelajar->count() }}</p>
            </div>
            <div style="background:white;padding:16px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
                <p style="margin:0 0 8px 0;color:#6b7280;font-size:13px;font-weight:600">KOSONG</p>
                @php
                    $empty = ($dorm->capacity ?? 0) - $pelajar->count();
                    $empty = max(0, $empty);
                @endphp
                <p style="margin:0;font-size:20px;font-weight:800" id="kosongCount">{{ $empty }}</p>
            </div>
        </div>
    @else
        <p style="color:#6b7280;padding:20px;text-align:center;background:#f9fafb;border-radius:8px">
            Tiada anggota direkodkan untuk dorm ini.
        </p>
    @endif
</div>

<script>
async function addMember(){
    const newIc = document.getElementById('newIc').value;
    const newName = document.getElementById('newName').value;
    const newJantina = document.getElementById('newJantina').value;

    if(!newIc || !newName || !newJantina){
        alert('Sila isi semua maklumat');
        return;
    }

    const dormCapacity = {{ $dorm->capacity ?? 'null' }};
    const currentCount = parseInt(document.getElementById('pelajarCount').textContent) || 0;
    
    if(dormCapacity !== null && currentCount >= dormCapacity){
        alert(`Kapasiti dorm penuh (${dormCapacity})`);
        return;
    }

    try {
        const res = await fetch("{{ route('dorms.tambahPelajar', $dorm->id_dorm) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                no_ic: newIc,
                nama: newName,
                jantina: newJantina
            })
        });

        const data = await res.json();

        if(res.ok && data.success){
            // Reload page to show new member
            location.reload();
        } else {
            alert(data.message || 'Ralat berlaku');
        }
    } catch(error) {
        console.error('Error:', error);
        alert('Ralat berlaku semasa menambah pelajar');
    }
}
</script>
@endsection
