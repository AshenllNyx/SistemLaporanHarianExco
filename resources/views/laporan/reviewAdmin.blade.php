@extends('layouts.app')

@section('content')
<div class="container">

    @php
        // Decode exco IC array
        $excos = json_decode($laporan->nama_exco, true);

        if (!is_array($excos)) {
            $excos = [$laporan->nama_exco];
        }

        // Dapatkan nama EXCO berdasarkan IC
        $senarai_exco = \App\Models\User::whereIn('no_ic', $excos)
                        ->get()
                        ->keyBy('no_ic');


        // Fungsi universal tukar IC → Nama
        function tukarKeNama($value) {
            // Jika bukan array, convert string → array
            if (!is_array($value)) {
                $value = explode(',', $value);
            }

            $value = array_filter(array_map('trim', $value));

            // Dapatkan pelajar
            $pelajar = \App\Models\User::whereIn('no_ic', $value)
                        ->get()
                        ->keyBy('no_ic');

            // Convert IC kepada nama
            return array_map(function($v) use ($pelajar) {
                return $pelajar[$v]->name ?? $v;
            }, $value);
        }

        // Fungsi detect jika array tu penuh IC
        function semuaIC($arr) {
            if (!is_array($arr)) return false;
            foreach ($arr as $item) {
                if (!preg_match('/^[0-9]{6,12}$/', $item)) {
                    return false;
                }
            }
            return true;
        }

    @endphp


    <h2 style="font-size:26px;font-weight:800;margin-bottom:25px">Semakan Laporan Harian EXCO</h2>

    {{-- TABLE MAKLUMAT ASAS --}}
    <table style="width:100%;border-collapse:collapse;margin-bottom:25px;font-size:15px;">
        <tr style="background:#f3f4f6">
            <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;width:25%">Butiran</th>
            <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;">Maklumat</th>
        </tr>

        {{-- EXCO BERTUGAS --}}
        <tr>
            <td style="padding:12px;border:1px solid #e5e7eb;font-weight:600">Exco Bertugas</td>
            <td style="padding:12px;border:1px solid #e5e7eb">
                <ul style="margin:0;padding-left:20px">
                    @foreach($excos as $ic)
                        <li>{{ $senarai_exco[$ic]->name ?? $ic }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>

        {{-- TARIKH --}}
        <tr>
            <td style="padding:12px;border:1px solid #e5e7eb;font-weight:600">Tarikh Laporan</td>
            <td style="padding:12px;border:1px solid #e5e7eb">
                {{ $laporan->tarikh_laporan }}
            </td>
        </tr>

        {{-- STATUS --}}
        <tr>
            <td style="padding:12px;border:1px solid #e5e7eb;font-weight:600">Status Laporan</td>
            <td style="padding:12px;border:1px solid #e5e7eb;text-transform:capitalize">
                {{ $laporan->status_laporan }}
            </td>
        </tr>
    </table>

    <hr style="margin:30px 0;">

    <h3 style="font-size:20px;font-weight:700;margin-bottom:20px">Butiran Laporan Mengikut Dorm</h3>

    {{-- LOOP SETIAP BUTIRAN --}}
    @foreach($laporan->butiranLaporans as $butiran)
        <div style="border:1px solid #e5e7eb;border-radius:10px;margin-bottom:20px;padding:18px;background:white;box-shadow:0 2px 5px rgba(0,0,0,0.05);">

            <h4 style="font-size:18px;font-weight:700;margin-bottom:10px;color:#2563eb">
                Dorm: {{ $butiran->dorm->nama_dorm ?? '–' }}
            </h4>

            <table style="width:100%;border-collapse:collapse;margin-bottom:10px">

                {{-- JENIS BUTIRAN --}}
                <tr>
                    <td style="width:25%;font-weight:600;padding:10px;border:1px solid #e5e7eb;background:#f9fafb">
                        Jenis Butiran
                    </td>
                    <td style="padding:10px;border:1px solid #e5e7eb">
                        {{ ucfirst($butiran->jenis_butiran) }}
                    </td>
                </tr>

                {{-- DESKRIPSI --}}
                <tr>
                    <td style="width:25%;font-weight:600;padding:10px;border:1px solid #e5e7eb;background:#f9fafb">
                        Deskripsi Isu
                    </td>
                    <td style="padding:10px;border:1px solid #e5e7eb">
                        {{ $butiran->deskripsi_isu ?? '-' }}
                    </td>
                </tr>


                {{-- DATA TAMBAHAN --}}
                @php
                    $extras = is_array($butiran->data_tambahan)
                        ? $butiran->data_tambahan
                        : json_decode($butiran->data_tambahan, true);
                @endphp

                @if($extras)
                    @foreach($extras as $key => $val)

                        @php
                            // NORMALISE semua jenis data → array string
                            if (is_array($val)) {
                                // convert object array → flat array
                                $val = array_map(function($v){
                                    if (is_array($v) && isset($v['ic'])) return $v['ic'];
                                    if (is_object($v) && isset($v->ic)) return $v->ic;
                                    return $v;
                                }, $val);
                            }

                            // jika nested object → flatten
                            if (is_object($val)) {
                                $val = (array)$val;
                            }

                            // string "ic1, ic2"
                            if (is_string($val) && str_contains($val, ',')) {
                                $val = array_map('trim', explode(',', $val));
                            }

                            // jika betul-betul IC array
                            $shouldConvert = is_array($val) && semuaIC($val);

                            if ($shouldConvert) {
                                $papar = tukarKeNama($val);
                            } else {
                                $papar = $val;
                            }

                        @endphp

                        <tr>
                            <td style="width:25%;font-weight:600;padding:10px;border:1px solid #e5e7eb;background:#f9fafb">
                                {{ ucfirst(str_replace('_',' ', $key)) }}
                            </td>
                            <td style="padding:10px;border:1px solid #e5e7eb">
                                @if(is_array($papar))
                                    {{ implode(', ', $papar) }}
                                @else
                                    {{ $papar ?: '-' }}
                                @endif
                            </td>
                        </tr>

                    @endforeach
                @endif

            </table>
        </div>
    @endforeach


    {{-- ACTIONS: Sahkan (left) and Kembali (right) --}}
    <div style="margin-top:30px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <form action="{{ route('laporan.pengesahan', $laporan->id_laporan) }}" method="POST" onsubmit="return confirm('Sahkan laporan ini?');">
                @csrf
                <button type="submit" class="btn btn-primary" 
                    style="padding:10px 20px;font-weight:600;font-size:16px;">
                    Sahkan Laporan Ini
                </button>
            </form>
        </div>

        <div>
            <a href="{{ route('homepage.admin') }}" class="btn btn-secondary" style="margin-top:0;">
                Kembali
            </a>
        </div>
    </div>

</div>
@endsection
