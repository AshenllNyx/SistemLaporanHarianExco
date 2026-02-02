@extends('layouts.app')

@section('title','Borang Laporan Pelajar Sakit')

@section('content')
<style>
    .searchable-select{position:relative;flex:1;min-width:0}
    .searchable-select .select-trigger{width:100%;padding:10px 36px 10px 12px;border-radius:8px;border:1px solid #d1d5db;background:#fff;cursor:pointer;text-align:left;font-size:14px;color:#111}
    .searchable-select .select-trigger::after{content:'';position:absolute;right:12px;top:50%;transform:translateY(-50%);border:6px solid transparent;border-top-color:#6b7280}
    .searchable-select.open .select-trigger{border-color:#2563eb;outline:2px solid rgba(37,99,235,0.2)}
    .searchable-select .select-dropdown{display:none;position:absolute;top:100%;left:0;right:0;margin-top:4px;background:#fff;border:1px solid #d1d5db;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,0.1);z-index:50;max-height:280px;overflow:hidden}
    .searchable-select.open .select-dropdown{display:block}
    .searchable-select .select-search{padding:8px;border-bottom:1px solid #e5e7eb}
    .searchable-select .select-search input{width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box}
    .searchable-select .select-search input:focus{outline:none;border-color:#2563eb}
    .searchable-select .select-options{max-height:220px;overflow-y:auto;padding:4px}
    .searchable-select .select-option{padding:10px 12px;cursor:pointer;font-size:14px;border-radius:6px}
    .searchable-select .select-option:hover,.searchable-select .select-option.highlight{background:#eff6ff;color:#1d4ed8}
    .searchable-select .select-option.empty{padding:12px;color:#6b7280;cursor:default}
    .searchable-select .select-placeholder{color:#9ca3af}
</style>

@include('components.report-steps', ['currentStep' => 4])

<div style="margin-bottom: 32px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">
        🏥 Laporan Pelajar Sakit
    </h2>
    <p style="color: #64748b; font-size: 15px;">Mencatat maklumat pelajar yang memerlukan rawatan atau sedang sakit.</p>
</div>

<div style="background:white;padding:32px;border-radius:24px;box-shadow:0 10px 40px rgba(0,0,0,0.05);border:1px solid #f1f5f9">
    <form method="POST" action="{{ route('laporan.pelajarsakit.store') }}">
        @csrf
        <input type="hidden" name="id_laporan" value="{{ $laporan->id_laporan }}">
        <input type="hidden" name="from" value="{{ $from ?? '' }}">

        <div style="margin-bottom:24px">
            <label style="font-weight:700;display:block;margin-bottom:12px;color:#1e293b;font-size:15px">👥 Pilih Pelajar Sakit</label>
            
            <script type="application/json" id="student-options-data">@json(collect($students)->map(fn($p, $ic) => ['value' => $ic, 'label' => $p['nama'] . ' — ' . $ic])->values()->all())</script>
            <div id="students-container">
                <div class="student-select-wrapper" style="margin-bottom:12px;display:flex;gap:12px;align-items:center">
                    <div class="searchable-select">
                        <input type="hidden" name="pelajar[]" value="" required>
                        <button type="button" class="select-trigger select-placeholder">-- Cari / Pilih Pelajar --</button>
                        <div class="select-dropdown">
                            <div class="select-search"><input type="text" placeholder="Taip nama atau no. IC..." autocomplete="off"></div>
                            <div class="select-options"></div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="add-student-btn" style="padding:10px 16px;border-radius:12px;background:#fef2f2;color:#ef4444;border:1px solid #fee2e2;font-size:14px;font-weight:700;cursor:pointer;margin-top:8px">
                + Tambah Pelajar Lain
            </button>
        </div>

        <div style="margin-bottom:24px">
            <label style="font-weight:700;display:block;margin-bottom:8px;color:#1e293b;font-size:15px">🏥 Jenis Sakit / Simptom</label>
            <input type="text" name="jenis_sakit" required 
                   style="width:100%;padding:12px;border-radius:12px;border:1px solid #e2e8f0;font-size:14px" 
                   placeholder="Cth: Demam panas, Sakit perut, Luka kecil" />
        </div>

        <div style="margin-bottom:24px">
            <label style="font-weight:700;display:block;margin-bottom:8px;color:#1e293b;font-size:15px">🛠️ Tindakan / Rawatan</label>
            <input type="text" name="tindakan" 
                   style="width:100%;padding:12px;border-radius:12px;border:1px solid #e2e8f0;font-size:14px" 
                   placeholder="Cth: Beri Panadol, Hantar ke klinik, Rehat di sickbay" />
        </div>

        <div style="margin-bottom:32px">
            <label style="font-weight:700;display:block;margin-bottom:8px;color:#1e293b;font-size:15px">📝 Catatan Tambahan</label>
            <textarea name="catatan" rows="4" 
                      style="width:100%;padding:12px;border-radius:12px;border:1px solid #e2e8f0;font-size:14px;resize:none"
                      placeholder="Masukkan butiran lanjut mengenai keadaan pelajar..."></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 32px;">
            <a href="{{ $from === 'hub' ? route('laporan.edit', $laporan->id_laporan) : route('laporan.dewanmakan.soalan', $laporan->id_laporan) }}" 
               style="color: #64748b; font-weight: 700; text-decoration: none; font-size: 15px;">
                {{ $from === 'hub' ? '⬅️ Batal & Kembali' : 'Langkau Bahagian Ini' }}
            </a>
            
            <button type="submit" 
                    style="background: #ef4444; color: white; padding: 14px 32px; border-radius: 14px; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(239,68,68,0.2); font-size: 15px;">
                {{ $from === 'hub' ? 'Simpan & Kembali ke Hub' : 'Simpan dan Teruskan ➔' }}
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('students-container');
    const addBtn = document.getElementById('add-student-btn');
    const optionsEl = document.getElementById('student-options-data');
    const studentOptions = optionsEl ? JSON.parse(optionsEl.textContent || '[]') : [];

    function renderOptions(selectEl, options, searchTerm) {
        const q = (searchTerm || '').toLowerCase().trim();
        const filtered = q ? options.filter(function(o) {
            return (o.label || '').toLowerCase().indexOf(q) !== -1;
        }) : options;
        const box = selectEl.querySelector('.select-options');
        if (filtered.length === 0) {
            box.innerHTML = '<div class="select-option empty">Tiada padanan.</div>';
            return;
        }
        box.innerHTML = filtered.map(function(o) {
            return '<div class="select-option" data-value="' + (o.value || '').replace(/"/g, '&quot;') + '" data-label="' + (o.label || '').replace(/"/g, '&quot;') + '">' + (o.label || '') + '</div>';
        }).join('');
    }

    function initSearchableSelect(selectEl) {
        const hidden = selectEl.querySelector('input[type="hidden"]');
        const trigger = selectEl.querySelector('.select-trigger');
        const searchInput = selectEl.querySelector('.select-search input');
        const optionsBox = selectEl.querySelector('.select-options');

        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.searchable-select.open').forEach(function(s) {
                if (s !== selectEl) s.classList.remove('open');
            });
            selectEl.classList.toggle('open');
            if (selectEl.classList.contains('open')) {
                searchInput.value = '';
                renderOptions(selectEl, studentOptions, '');
                searchInput.focus();
            }
        });

        searchInput.addEventListener('input', function() {
            renderOptions(selectEl, studentOptions, searchInput.value);
        });

        optionsBox.addEventListener('click', function(e) {
            const opt = e.target.closest('.select-option');
            if (!opt || opt.classList.contains('empty')) return;
            const val = opt.getAttribute('data-value');
            const label = opt.getAttribute('data-label');
            hidden.value = val;
            hidden.removeAttribute('required');
            trigger.textContent = label;
            trigger.classList.remove('select-placeholder');
            selectEl.classList.remove('open');
        });

        selectEl.addEventListener('click', function(e) { e.stopPropagation(); });
        renderOptions(selectEl, studentOptions, '');
    }

    document.querySelectorAll('#students-container .searchable-select').forEach(initSearchableSelect);

    document.addEventListener('click', function() {
        document.querySelectorAll('.searchable-select.open').forEach(function(s) {
            s.classList.remove('open');
        });
    });

    addBtn.addEventListener('click', function() {
        const firstWrapper = container.querySelector('.student-select-wrapper');
        const clone = firstWrapper.cloneNode(true);
        const selectEl = clone.querySelector('.searchable-select');
        const hidden = selectEl.querySelector('input[type="hidden"]');
        const trigger = selectEl.querySelector('.select-trigger');
        hidden.value = '';
        hidden.setAttribute('required', 'required');
        trigger.textContent = '-- Cari / Pilih Pelajar --';
        trigger.classList.add('select-placeholder');
        
        let removeBtn = clone.querySelector('.remove-student-btn');
        if (!removeBtn) {
            removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-student-btn';
            removeBtn.style.cssText = 'padding:10px 16px;border-radius:12px;background:#fef2f2;color:#ef4444;border:1px solid #fee2e2;font-size:14px;font-weight:700;cursor:pointer;margin-left:8px';
            removeBtn.textContent = '✕';
            clone.appendChild(removeBtn);
        }
        removeBtn.addEventListener('click', function() { clone.remove(); });
        container.appendChild(clone);
        initSearchableSelect(selectEl);
    });
});
</script>
@endsection