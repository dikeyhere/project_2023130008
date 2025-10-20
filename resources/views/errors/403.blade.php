@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Akses Ditolak (Error 403)</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h4><i class="icon fas fa-ban"></i> Maaf!</h4>
                        Anda tidak memiliki hak akses untuk halaman ini. Role Anda:
                        <strong>{{ auth()->user()->role ?? 'Guest' }}</strong>.<br>
                        <small>Fitur ini hanya untuk role tertentu. Silakan hubungi admin.</small>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
