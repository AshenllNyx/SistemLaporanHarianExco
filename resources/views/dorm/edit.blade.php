@extends('layouts.app')

@section('title', 'Edit Dorm - ' . $dorm->nama_dorm)

@section('content')
<div style="max-width:900px;margin:0 auto">
    <h2 style="font-size:22px;font-weight:700;margin-bottom:20px">
        Edit Dorm: {{ $dorm->nama_dorm }}
    </h2>

    {{-- FORM DORM --}}
    <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06);margin-bottom:20px">
        <form action="{{ route('dorms.update', $dorm->id_dorm) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));gap:20px;margin-bottom:20px">
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:6px">Nama Dorm</label>
                    <input type="text" name="nama_dorm"
                        value="{{ old('nama_dorm', $dorm->nama_dorm) }}"
                        style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:10px;font-size:16px">
                </div>

                <div>
                    <label style="font-weight:600;display:block;margin-bottom:6px">Blok</label>
                    <input type="text" name="blok"
                        value="{{ old('blok', $dorm->blok) }}"
                        style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:10px;font-size:16px">
                </div>

                <div>
                    <label style="font-weight:600;display:block;margin-bottom:6px">Kapasiti</label>
                    <input type="number" name="capacity"
                        value="{{ old('capacity', $dorm->capacity) }}"
                        style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:10px;font-size:16px">
                </div>
            </div>

            <input type="hidden" name="senarai_pelajar" id="senaraiPelajarField" value="[]">

            <div style="text-align:right">
                <button style="width:100%; max-width: 300px; padding:12px 24px;border-radius:12px;background:#2563eb;color:white;border:none;font-weight:700;cursor:pointer;box-shadow: 0 4px 12px rgba(37,99,235,0.2)">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- SENARAI ANGGOTA --}}
    <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 6px 18px rgba(2,6,23,0.06)">
        <h3 style="font-size:18px;font-weight:700;margin-bottom:6px">Senarai Anggota Dorm</h3>

        <p style="font-size:13px;color:#6b7280;margin-bottom:16px">
            Kapasiti: {{ $dorm->capacity ?? 'Tanpa had' }} |
            Digunakan: <span id="usedCount">{{ $dorm->pelajars->count() }}</span>
        </p>

        {{-- TAMBAH --}}
        <div style="background:#f0f9ff;padding:20px;border-radius:16px;border:1px solid #bfdbfe;margin-bottom:24px">
            <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));gap:12px">
                <input id="newIc" placeholder="No. IC"
                    style="padding:12px;border:1px solid #60a5fa;border-radius:10px;font-size:16px">
                <input id="newName" placeholder="Nama Lengkap"
                    style="padding:12px;border:1px solid #60a5fa;border-radius:10px;font-size:16px">
                <select id="newJantina"
                    style="padding:12px;border:1px solid #60a5fa;border-radius:10px;font-size:16px">
                    <option value="">Jantina</option>
                    <option value="L">Lelaki</option>
                    <option value="P">Perempuan</option>
                </select>
                <button onclick="addMemberAjax()"
                    style="padding:12px 24px;background:#3b82f6;color:white;border:none;border-radius:10px;font-weight:700;cursor:pointer">
                    Tambah Anggota
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table style="width:100%;border-collapse:collapse;min-width:600px">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                        <th style="padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em">No</th>
                        <th style="padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em">No. IC</th>
                        <th style="padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em">Nama</th>
                        <th style="padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em">Jantina</th>
                        <th style="padding:12px;text-align:center;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em">Tindakan</th>
                    </tr>
                </thead>
                <tbody id="membersList"></tbody>
            </table>
        </div>

        <p id="emptyState" style="text-align:center;padding:30px;color:#6b7280">
            Tiada anggota
        </p>
    </div>
</div>

@php
    $membersArray = $dorm->pelajars->map(function($pelajar) {
        return [
            'id' => $pelajar->id,
            'no_ic' => $pelajar->no_ic,
            'nama' => $pelajar->nama,
            'jantina' => $pelajar->jantina
        ];
    })->values()->toArray();
@endphp

<script>
let members = @json($membersArray);

const dormCapacity = {{ $dorm->capacity ?? 'null' }};

function updateUI(){
    usedCount.innerText = members.length;
    const list = membersList;
    emptyState.style.display = members.length ? 'none' : 'block';

    list.innerHTML = members.map((m,i)=>`
        <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:12px">${i+1}</td>

            <td style="padding:12px">
                <input id="ic_${m.id}" value="${m.no_ic}"
                    style="width:100%;padding:6px;border:1px solid #d1d5db;border-radius:6px">
            </td>

            <td style="padding:12px">
                <input id="nama_${m.id}" value="${m.nama}"
                    style="width:100%;padding:6px;border:1px solid #d1d5db;border-radius:6px">
            </td>

            <td style="padding:12px">
                <select id="jantina_${m.id}"
                    style="padding:8px 12px;border-radius:8px;border:1px solid #d1d5db;font-size:14px;background:white">
                    <option value="L" ${m.jantina=='L'?'selected':''}>Lelaki</option>
                    <option value="P" ${m.jantina=='P'?'selected':''}>Perempuan</option>
                </select>
            </td>

            <td style="padding:12px;text-align:center;display:flex;gap:8px;justify-content:center">
                <button onclick="updateMember(${m.id})"
                    style="padding:8px 16px;background:#22c55e;color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px">
                    Update
                </button>
                <button onclick="removeMember(${i})"
                    style="padding:8px 16px;background:#ef4444;color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px">
                    Padam
                </button>
            </td>
        </tr>
    `).join('');

    // senaraiPelajarField is kept for backward compatibility but not used anymore
    // Members are now managed via API calls to pelajars table
}

updateUI();

/* === FUNCTIONS === */

async function removeMember(i){
    if(!confirm('Padam anggota ini?')){
        return;
    }
    
    const member = members[i];
    if(!member || !member.id){
        members.splice(i,1);
        updateUI();
        return;
    }

    // Delete from database via API
    try {
        const res = await fetch(`/dorms/{{ $dorm->id_dorm }}/pelajar/${member.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if(res.ok || res.status === 404){
            members.splice(i,1);
            updateUI();
        } else {
            const data = await res.json();
            alert(data.message || 'Gagal memadam');
        }
    } catch(error) {
        console.error('Error:', error);
        alert('Ralat berlaku semasa memadam');
    }
}

async function addMemberAjax(){
    if(dormCapacity !== null && members.length >= dormCapacity){
        alert(`Kapasiti dorm penuh (${dormCapacity})`);
        return;
    }

    if(!newIc.value || !newName.value || !newJantina.value){
        alert('Sila isi semua maklumat');
        return;
    }

    const res = await fetch("{{ route('dorms.tambahPelajar', $dorm->id_dorm) }}",{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({
            no_ic:newIc.value,
            nama:newName.value,
            jantina:newJantina.value
        })
    });

    const data = await res.json();

    if(res.ok){
        members.push({
            id: data.pelajar.id,
            no_ic: data.pelajar.no_ic,
            nama: data.pelajar.nama,
            jantina: data.pelajar.jantina
        });
        updateUI();
        newIc.value=newName.value=newJantina.value='';
    }else alert(data.message || 'Ralat berlaku');
}

async function updateMember(id){
    const res = await fetch(
        "{{ route('dorms.updatePelajar', [$dorm->id_dorm,'PEL']) }}".replace('PEL',id),
        {
            method:'PUT',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({
                no_ic:document.getElementById(`ic_${id}`).value,
                nama:document.getElementById(`nama_${id}`).value,
                jantina:document.getElementById(`jantina_${id}`).value
            })
        }
    );

    const data = await res.json();
    if(res.ok){
        const index = members.findIndex(m=>m.id===id);
        if(index !== -1){
            members[index] = {
                id: data.pelajar.id,
                no_ic: data.pelajar.no_ic,
                nama: data.pelajar.nama,
                jantina: data.pelajar.jantina
            };
        }
        updateUI();
        alert('Berjaya kemaskini');
    } else alert(data.message || 'Ralat berlaku');
}
</script>
@endsection
