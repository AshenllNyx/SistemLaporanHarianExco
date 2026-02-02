@extends('layouts.app')

@section('title','Pilih Laporan Seterusnya')

@section('content')
@include('components.report-steps', ['currentStep' => 5])
<style>
    .question-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
        min-height: 60vh;
    }

    .question-card {
        background: white;
        padding: 48px 32px;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        text-align: center;
        max-width: 480px;
        width: 100%;
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
    }

    .question-card:hover {
        transform: translateY(-5px);
    }

    .icon-box {
        width: 84px;
        height: 84px;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 28px;
        font-size: 36px;
        box-shadow: 0 8px 16px rgba(22, 163, 74, 0.1);
    }

    .q-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
        letter-spacing: -0.02em;
    }

    .q-text {
        color: #64748b;
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 36px;
    }

    .btn-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .btn-q {
        padding: 16px 24px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        width: 100%;
    }

    .btn-ya {
        background: #16a34a;
        color: white;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
    }

    .btn-ya:hover {
        background: #15803d;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(22, 163, 74, 0.3);
    }

    .btn-tidak {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-tidak:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
</style>

<div class="question-wrap">
    <div class="question-card">
        <div class="icon-box">🍽️</div>
        <h2 class="q-title">Laporan Dewan Makan</h2>
        <p class="q-text">Adakah terdapat sebarang isu berkaitan kualiti makanan atau kebersihan di Dewan Makan?</p>

        <div class="btn-grid">
            <form method="GET" action="{{ route('laporan.dewanmakan.create', $laporan->id_laporan) }}">
                <button type="submit" class="btn-q btn-ya">Ya, Ada</button>
            </form>

            <form method="GET" action="{{ route('laporan.review', $laporan->id_laporan) }}">
                <button type="submit" class="btn-q btn-tidak">Tidak</button>
            </form>
        </div>
    </div>
</div>
@endsection