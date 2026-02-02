@extends('layouts.app')

@section('content')
@include('components.report-steps', ['currentStep' => 6])

<div class="container" style="padding-top: 0;">

    @php
        $excos = json_decode($laporan->nama_exco, true);
        if (!is_array($excos)) {
            $excos = [$laporan->nama_exco];
        }

        $senarai_exco = \App\Models\User::whereIn('no_ic', $excos)
                        ->get()
                        ->keyBy('no_ic');

        if (!function_exists('tukarKeNama')) {
            function tukarKeNama($value) {
                if (!is_array($value)) {
                    $value = explode(',', $value);
                }
                $value = array_filter(array_map('trim', $value));
                $pelajar = \App\Models\Pelajar::whereIn('no_ic', $value)
                            ->get()
                            ->keyBy('no_ic');
                return array_map(function($v) use ($pelajar) {
                    return $pelajar[$v]->nama ?? $v;
                }, $value);
            }
        }

        if (!function_exists('semuaIC')) {
            function semuaIC($arr) {
                if (!is_array($arr)) return false;
                foreach ($arr as $item) {
                    if (!preg_match('/^[0-9]{6,12}$/', $item)) {
                        return false;
                    }
                }
                return true;
            }
        }
    @endphp

    <div style="margin-bottom: 32px;">
        <h2 style="font-size: 28px; font-weight: 800; letter-spacing: -0.02em; color: var(--text-main);">
            📋 Semakan Laporan Harian
        </h2>
        <p style="color: var(--text-muted); font-size: 15px;">Sila semak maklumat sebelum menghantar laporan rasmi.</p>
    </div>

    <div class="card" style="border-left: 4px solid var(--accent); padding: 0; overflow: hidden;">
        <div style="background: #f8fafc; padding: 16px 24px; border-bottom: 1px solid var(--border);">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main);">Maklumat Asas Laporan</h3>
        </div>
        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 32px;">
                <div>
                    <label style="color: var(--muted); text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; margin-bottom: 4px;">Exco Bertugas</label>
                    <div style="font-weight: 700; color: var(--text-main);">
                        @foreach($excos as $ic)
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span style="font-size: 14px;">🔹</span>
                                {{ $senarai_exco[$ic]->name ?? $ic }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label style="color: var(--muted); text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; margin-bottom: 4px;">Tarikh Laporan</label>
                    <div style="font-weight: 700; color: var(--text-main); font-size: 16px;">
                        📅 {{ \Carbon\Carbon::parse($laporan->tarikh_laporan)->format('d F Y') }}
                    </div>
                </div>
                <div>
                    <label style="color: var(--muted); text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; margin-bottom: 4px;">Status</label>
                    <div>
                        <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                            {{ $laporan->status_laporan }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="margin: 48px 0 24px 0;">
        <h3 style="font-size: 20px; font-weight: 800; color: var(--text-main);">Butiran Laporan</h3>
    </div>

    @foreach($laporan->butiranLaporans as $butiran)
        <div class="card" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h4 style="font-size: 18px; font-weight: 700; color: var(--accent);">
                    🏢 {{ $butiran->dorm->nama_dorm ?? '–' }}
                </h4>
                <div style="background: #f1f5f9; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--text-muted);">
                    {{ ucfirst($butiran->jenis_butiran) }}
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                @if($butiran->deskripsi_isu)
                <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                    <label style="color: var(--muted); font-size: 12px; margin-bottom: 4px;">Deskripsi Isu</label>
                    <div style="font-weight: 500;">{{ $butiran->deskripsi_isu }}</div>
                </div>
                @endif

                @php
                    $extras = is_array($butiran->data_tambahan)
                        ? $butiran->data_tambahan
                        : json_decode($butiran->data_tambahan, true);
                @endphp

                @if($extras)
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                    @foreach($extras as $key => $val)
                        @php
                            if (is_array($val)) {
                                $val = array_map(function($v){
                                    if (is_array($v) && isset($v['ic'])) return $v['ic'];
                                    if (is_object($v) && isset($v->ic)) return $v->ic;
                                    return $v;
                                }, $val);
                            }
                            if (is_object($val)) { $val = (array)$val; }
                            if (is_string($val) && str_contains($val, ',')) {
                                $val = array_map('trim', explode(',', $val));
                            }
                            $shouldConvert = is_array($val) && semuaIC($val);
                            $papar = $shouldConvert ? tukarKeNama($val) : $val;

                            $isKebersihan = $key == 'kategori' || $key == 'kategori_kebersihan';
                        @endphp

                        <div style="background: white; padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <label style="color: var(--muted); font-size: 12px; margin-bottom: 4px; text-transform: capitalize;">
                                {{ str_replace('_',' ', $key) }}
                            </label>
                            <div style="font-weight: 700; color: {{ $isKebersihan ? 'var(--accent)' : 'var(--text-main)' }};">
                                @if(is_array($papar))
                                    @if(count($papar) > 0)
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
                                            @foreach($papar as $p)
                                                <span style="background: #eff6ff; color: #1d4ed8; padding: 2px 10px; border-radius: 6px; font-size: 13px;">{{ $p }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color: var(--muted); font-weight: 400; font-style: italic;">Tiada</span>
                                    @endif
                                @else
                                    {{ $papar ?: '-' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <div style="margin-top: 48px; display: flex; justify-content: space-between; align-items: center; padding-bottom: 60px;">
        <a href="{{ route('homepage') }}" class="btn btn-secondary">
            ⬅️ Kembali
        </a>
        @if(in_array($laporan->status_laporan, ['draf', 'hantar_semula', 'perlu_hantar_semula', 'tolak']))
        <form action="{{ route('laporan.submit', $laporan->id_laporan) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="padding: 16px 32px; font-size: 16px;">
                🚀 Hantar Laporan Rasmi
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
