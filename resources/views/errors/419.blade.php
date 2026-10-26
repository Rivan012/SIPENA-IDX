@extends('errors.layout')

@section('title', 'Sesi Halaman Kedaluwarsa')
@section('code', '419')
@section('message', 'Masa aktif sesi telah berakhir demi keamanan data Anda. Silakan muat ulang halaman ini untuk memperbarui token sesi.')

@section('actions')
    <button type="button" onclick="window.location.reload()" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
        Muat Ulang
    </button>
@endsection
