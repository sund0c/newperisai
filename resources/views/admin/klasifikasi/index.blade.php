{{-- resources/views/admin/klasifikasi/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Klasifikasi Aset')
@section('page-title', 'Klasifikasi Aset')
@section('page-subtitle', 'Referensi klasifikasi aset sistem informasi Pemerintah Provinsi Bali')

@section('content')



    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Data klasifikasi bersifat <strong class="text-gray-700">referensi sistem</strong>. Klik nama klasifikasi
                untuk melihat sub klasifikasi.</span>
        </div>

        {{-- Tabel --}}
        @if ($klasifikasis->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada data klasifikasi aset.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[600px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                #</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Klasifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($klasifikasis as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-xs text-gray-400 text-center">
                                    <a href="{{ route('admin.klasifikasi.show', $item) }}"
                                        class="block">{{ $loop->iteration }}</a>
                                </td>

                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.klasifikasi.show', $item) }}" class="block">
                                        <span
                                            class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-mono font-semibold text-gray-700">
                                            {{ $item->kodeklas }}
                                        </span>
                                    </a>
                                </td>

                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.klasifikasi.show', $item) }}"
                                        class="block font-medium text-gray-900 hover:text-blue-600 transition-colors">
                                        {{ $item->klasifikasiaset }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

@endsection
