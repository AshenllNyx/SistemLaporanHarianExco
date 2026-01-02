@extends('layouts.app')

@section('title', 'Edit Profil Admin')

@section('content')
<style>
    .profile-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .profile-title {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .profile-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        color: #1f2937;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-input:disabled {
        background-color: #f3f4f6;
        color: #9ca3af;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 28px;
    }

    .btn-submit {
        flex: 1;
        padding: 10px 16px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-submit:hover {
        background: #1d4ed8;
    }

    .btn-cancel {
        flex: 1;
        padding: 10px 16px;
        background: #e5e7eb;
        color: #374151;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }

    .alert {
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .form-error {
        color: #dc2626;
        font-size: 12px;
        margin-top: 4px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .badge-admin {
        display: inline-block;
        background: #dbeafe;
        color: #1e40af;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 4px;
    }

    @media (max-width: 600px) {
        .profile-container {
            padding: 20px;
        }

        .info-row {
            grid-template-columns: 1fr;
        }

        .button-group {
            flex-direction: column;
        }
    }
</style>

<div class="profile-container">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
        <h2 class="profile-title" style="margin-bottom: 0;">Edit Profil Admin</h2>
        <span class="badge-admin">👤 ADMIN</span>
    </div>
    <p class="profile-subtitle">Kemaskini maklumat profil admin anda</p>

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Ada kesalahan!</strong>
            <ul style="margin-top: 8px; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="info-row">
            <div class="form-group">
                <label class="form-label" for="no_ic">No. IC</label>
                <input type="text" id="no_ic" name="no_ic" class="form-input" value="{{ old('no_ic', Auth::user()->no_ic) }}" disabled>
            </div>

            <div class="form-group">
                <label class="form-label" for="user_name">Nama Pengguna</label>
                <input type="text" id="user_name" name="user_name" class="form-input" value="{{ old('user_name', Auth::user()->user_name) }}" disabled>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="name">Nama Penuh</label>
            <input type="text" id="name" name="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name', Auth::user()->name) }}" required>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="info-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input @error('email') border-red-500 @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="jantina">Jantina</label>
                <select id="jantina" name="jantina" class="form-input @error('jantina') border-red-500 @enderror">
                    <option value="">-- Pilih Jantina --</option>
                    <option value="Lelaki" {{ old('jantina', Auth::user()->jantina) == 'Lelaki' ? 'selected' : '' }}>Lelaki</option>
                    <option value="Perempuan" {{ old('jantina', Auth::user()->jantina) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jantina')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Kata Laluan Baru (Tinggalkan kosong jika tidak ingin ubah)</label>
            <input type="password" id="password" name="password" class="form-input @error('password') border-red-500 @enderror" placeholder="Masukkan kata laluan baru">
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Sahkan Kata Laluan</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Sahkan kata laluan baru">
        </div>

        <div class="button-group">
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
            <a href="{{ route('homepage.admin') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>

@endsection
