@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<style>
    .page-title {
        font-size: 22px;
        font-weight: 700;
        color:#0f172a;
        margin-bottom: 24px;
    }

    .form-container {
        background: white;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(2,6,23,0.08);
        border: 1px solid rgba(15,23,42,0.04);
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        color: #0f172a;
        box-sizing: border-box;
        transition: all 0.2s;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-error {
        color: #dc2626;
        font-size: 12px;
        margin-top: 4px;
    }

    .form-group.has-error .form-input,
    .form-group.has-error .form-select {
        border-color: #dc2626;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 30px;
    }

    .btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-save {
        background: #2563eb;
        color: white;
    }

    .btn-save:hover {
        background: #1d4ed8;
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #0f172a;
        border: 1px solid #e5e7eb;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
    }

    .info-box {
        background: #dbeafe;
        border-left: 4px solid #2563eb;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #0c4a6e;
    }

    .form-readonly {
        background: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
    }
</style>

<h2 class="page-title">Edit Pengguna - {{ $editUser->name }}</h2>

<div class="form-container">
    <div class="info-box">
        <strong>No IC:</strong> {{ $editUser->no_ic }} | <strong>Berdaftar:</strong> {{ $editUser->created_at->format('d/m/Y H:i') }}
    </div>

    <form action="{{ route('users.update', $editUser->no_ic) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nama -->
        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
            <label class="form-label">Nama Penuh <span style="color: #dc2626;">*</span></label>
            <input 
                type="text" 
                name="name" 
                class="form-input"
                value="{{ old('name', $editUser->name) }}"
                required
            >
            @error('name')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Username -->
        <div class="form-group {{ $errors->has('user_name') ? 'has-error' : '' }}">
            <label class="form-label">Username <span style="color: #dc2626;">*</span></label>
            <input 
                type="text" 
                name="user_name" 
                class="form-input"
                value="{{ old('user_name', $editUser->user_name) }}"
                required
            >
            @error('user_name')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            <label class="form-label">Email <span style="color: #dc2626;">*</span></label>
            <input 
                type="email" 
                name="email" 
                class="form-input"
                value="{{ old('email', $editUser->email) }}"
                required
            >
            @error('email')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jantina -->
        <div class="form-group {{ $errors->has('jantina') ? 'has-error' : '' }}">
            <label class="form-label">Jantina <span style="color: #dc2626;">*</span></label>
            <select name="jantina" class="form-select" required>
                <option value="">-- Pilih Jantina --</option>
                <option value="Lelaki" {{ old('jantina', $editUser->jantina) === 'Lelaki' ? 'selected' : '' }}>Lelaki</option>
                <option value="Perempuan" {{ old('jantina', $editUser->jantina) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jantina')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Level -->
        <div class="form-group {{ $errors->has('level') ? 'has-error' : '' }}">
            <label class="form-label">Tahap Pengguna <span style="color: #dc2626;">*</span></label>
            <select name="level" class="form-select" required>
                <option value="">-- Pilih Tahap --</option>
                <option value="user" {{ old('level', $editUser->level) === 'user' ? 'selected' : '' }}>Pengguna Biasa</option>
                <option value="admin" {{ old('level', $editUser->level) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('level')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="button-group">
            <button type="submit" class="btn btn-save">Simpan Perubahan</button>
            <a href="{{ route('users.index') }}" class="btn btn-cancel" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">Batal</a>
        </div>
    </form>
</div>

@endsection
