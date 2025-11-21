@extends('layouts.app')

@section('title', 'Senarai Pengguna')

@section('content')
<style>
    .page-title {
        font-size: 22px;
        font-weight: 700;
        color:#0f172a;
        margin-bottom: 16px;
    }

    .table-box {
        background:white;
        padding:20px;
        border-radius:14px;
        box-shadow:0 10px 30px rgba(2,6,23,0.08);
        border:1px solid rgba(15,23,42,0.04);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top:10px;
        border-radius: 10px;
        overflow: hidden;
    }

    th {
        background: #2563eb;
        color:white;
        padding:14px;
        text-align:left;
        font-size:14px;
    }
    td {
        padding:12px 14px;
        border-bottom:1px solid #e2e8f0;
        font-size:14px;
        color:#0f172a;
    }
    tr:hover{
        background:#eef2ff;
    }
</style>

<h2 class="page-title">Senarai Pengguna</h2>

<div class="table-box">
<table>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Username</th>
        <th>Email</th>
        <th>Jantina</th>
    </tr>

    @foreach($users as $index => $user)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->user_name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->jantina }}</td>
    </tr>
    @endforeach
</table>
</div>

@endsection
