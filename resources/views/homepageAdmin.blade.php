@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

@php
    // Fallback variables
    $totalLaporans = $totalLaporans ?? 0;
    $draftCount = $draftCount ?? 0;
    $submittedCount = $submittedCount ?? 0;
    $resubmitCount = $resubmitCount ?? 0;
@endphp

<style>
    /* Admin Dashboard Shared Styles */
    .admin-wrap {
        width: 95%;
        max-width: 1200px;
        margin: 30px auto;
        font-family: 'Inter', sans-serif;
    }

    .admin-header {
        margin-bottom: 30px;
    }

    .admin-title {
        font-size: 28px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 6px;
    }

    .admin-subtitle {
        font-size: 15px;
        color: #6b7280;
    }

    /* Stat Cards (Modern Premium Look) */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    /* New Split Layout Styles */
    .dashboard-top {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 1024px) {
        .dashboard-top {
            grid-template-columns: 1fr;
        }
    }

    .large-stat-card {
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
        color: white;
        padding: 40px 32px;
        border-radius: 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.2);
        border: none;
        height: 100%;
    }

    .large-stat-card .stat-label {
        color: rgba(255, 255, 255, 0.8) !important;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .large-stat-card .stat-number {
        font-size: 64px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 8px;
    }

    .side-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: #fff;
        padding: 24px;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); 
        border: 1px solid rgba(229, 231, 235, 0.5);
        display: flex;
        align-items: center;
        gap: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        border-color: rgba(229, 231, 235, 0.8);
    }

    /* Icon Box */
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(3deg);
    }

    .stat-content {
        flex: 1;
    }

    /* Color Variants with Soft Gradients */
    .stat-card.blue .stat-icon { background: linear-gradient(135deg, #eff6ff 0%, #3b82f6 100%); color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); }
    .stat-card.yellow .stat-icon { background: linear-gradient(135deg, #fffbeb 0%, #f59e0b 100%); color: white; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3); }
    .stat-card.green .stat-icon { background: linear-gradient(135deg, #f0fdf4 0%, #10b981 100%); color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }
    .stat-card.red .stat-icon { background: linear-gradient(135deg, #fef2f2 0%, #ef4444 100%); color: white; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3); }
    .stat-card.purple .stat-icon { background: linear-gradient(135deg, #f5f3ff 0%, #8b5cf6 100%); color: white; box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3); }

    .stat-label {
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 4px;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -0.03em;
    }

    @keyframes pulse-soft {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    .pulse-hover:hover {
        animation: pulse-soft 2s infinite;
    }

    .stat-icon {
        font-size: 32px;
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.15) rotate(5deg);
    }

</style>

<div class="admin-wrap" style="width: 100%; max-width: 1200px; margin: 20px auto; padding: 0 15px;">

    {{-- Header --}}
    <div class="admin-header">
        <h1 class="admin-title">📊 Admin Dashboard</h1>
        <p class="admin-subtitle">Statistik dan ringkasan Laporan Harian EXCO</p>
    </div>

    {{-- Top Section: Focus Stats --}}
    <div class="dashboard-top">
        {{-- Left Focal Point: Total Students --}}
        <div class="large-stat-card">
            <div style="font-size: 40px; margin-bottom: 16px;">👥</div>
            <div class="stat-label">BILANGAN PELAJAR</div>
            <div class="stat-number">{{ $totalStudents }}</div>
            <div style="font-size: 14px; opacity: 0.9; font-weight: 500;">Jumlah Pelajar Berdaftar</div>
        </div>

        {{-- Right Side: Dynamic Stats (Checklist/Reports) --}}
        <div class="side-grid">
            {{-- Attendance Today --}}
            <div class="stat-card green" style="background: white; border-radius: 20px; padding: 24px; display: flex; align-items: center; gap: 20px; border: 1px solid #f3f4f6;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); color: #16a34a;">✅</div>
                <div class="stat-content">
                    <div class="stat-label">KEHADIRAN HARI INI</div>
                    <div class="stat-number">
                        {{ $todayPresent }} <span style="font-size: 18px; color: #9ca3af; font-weight: 500;">/ {{ $totalStudents }}</span>
                    </div>
                </div>
            </div>

            {{-- Total Reports --}}
            <div class="stat-card blue">
                <div class="stat-icon">📑</div>
                <div class="stat-content">
                    <div class="stat-label">Jumlah Laporan</div>
                    <div class="stat-number">{{ $totalLaporans }}</div>
                </div>
            </div>

            {{-- Submitted --}}
            <div class="stat-card green">
                <div class="stat-icon">📤</div>
                <div class="stat-content">
                    <div class="stat-label">Laporan Dihantar</div>
                    <div class="stat-number">{{ $submittedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats (Small Grid) --}}
    <div class="stat-grid">
        {{-- Draft --}}
        <div class="stat-card yellow">
            <div class="stat-icon">✏️</div>
            <div class="stat-content">
                <div class="stat-label">Dalam Draf</div>
                <div class="stat-number">{{ $draftCount }}</div>
            </div>
        </div>

        {{-- Resubmit --}}
        <div class="stat-card red">
            <div class="stat-icon">⚠️</div>
            <div class="stat-content">
                <div class="stat-label">Perlu Tindakan</div>
                <div class="stat-number">{{ $resubmitCount }}</div>
                <div class="stat-subtext">Hantar semula</div>
            </div>
        </div>

        {{-- Action Button (Placeholder for better symmetry) --}}
        <div style="display: flex; align-items: center;">
            <a href="{{ route('semakan.Laporan') }}" class="action-btn" style="width: 100%; margin: 0; padding: 20px;">
                🔎 Semak Semua Laporan
            </a>
        </div>
    </div>



@endsection