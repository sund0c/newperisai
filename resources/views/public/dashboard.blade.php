@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di Sistem Aduan CSIRT Bali')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-2">Belum Ada Laporan</h3>
        <p class="text-sm text-gray-400 mb-4">Laporkan insiden atau kerentanan keamanan siber kepada CSIRT Bali.</p>
        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Laporan Baru
        </a>
    </div>
</div>
@endsection
