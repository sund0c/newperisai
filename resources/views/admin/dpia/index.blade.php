{{-- resources/views/admin/dpia/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Data Protection Impact Assessment (DPIA)')
@section('page-title', 'Data Protection Impact Assessment (DPIA)')
@section('page-subtitle', 'Penilaian dampak privasi aktivitas pemrosesan data · Tahun ' . ($tahunContext?->tahun ??
    '-'))

@section('content')

    {{-- FILTER BAR --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.dpia.index') }}">
            <div class="px-6 py-4 flex items-center gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama aktivitas..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @if (auth()->user()->hasRole(['admin']))
                    <div class="flex-1">
                        <select name="opd_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua OPD</option>
                            @foreach ($opds as $opd)
                                <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                                    {{ $opd->namaopd }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['search', 'opd_id']))
                        <a href="{{ route('admin.dpia.index') }}"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- FLASH --}}
    @if (session('success'))
        <div
            class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm mb-4">
            <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{!! session('success') !!}</span>
        </div>
    @endif
    @if (session('error'))
        <div
            class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm mb-4">
            <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{!! session('error') !!}</span>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-800">Daftar DPIA</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    Menampilkan <strong class="text-gray-700">{{ $dpias->count() }}</strong>
                    dari total <strong class="text-gray-700">{{ $dpias->total() }}</strong> dokumen
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="document.getElementById('modalExportPDF').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50
                           px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </button>
                @if ($tahunContext?->is_active)
                    <a href="{{ route('admin.dpia.create') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700
                               px-3 py-1.5 text-sm font-semibold text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Data
                    </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-10">#
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">
                            Kode DPIA</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                            Ref. RoPA</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama
                            Aktivitas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">OPD
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">
                            Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($dpias as $dpia)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-3 text-xs text-gray-400 font-mono">
                                {{ ($dpias->currentPage() - 1) * $dpias->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.dpia.edit', $dpia) }}"
                                    class="inline-flex items-center font-mono text-xs font-semibold
                                       text-indigo-600 bg-indigo-50 hover:bg-indigo-100
                                       border border-indigo-200 px-2.5 py-1 rounded-lg transition-colors">
                                    {{ $dpia->kode }}
                                </a>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.ropa.edit', $dpia->ropaActivity) }}"
                                    class="inline-flex items-center font-mono text-xs font-medium
                                       text-gray-500 bg-gray-100 hover:bg-gray-200
                                       border border-gray-200 px-2 py-0.5 rounded transition-colors">
                                    {{ $dpia->ropaActivity?->kode ?? '-' }}
                                </a>
                            </td>
                            <td class="px-6 py-3 max-w-xs">
                                <a href="{{ route('admin.dpia.edit', $dpia) }}"
                                    class="text-xs font-medium text-gray-800 hover:text-indigo-600 transition-colors font-mono">
                                    {{ $dpia->nama_aktivitas }}
                                </a>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-xs text-gray-800 font-mono">{{ $dpia->opd?->namaopd ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs text-gray-800 font-mono">
                                    {{ $dpia->tanggal_penyusunan?->format('d/m/Y') ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.dpia.detail-pdf', $dpia) }}" target="_blank"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg
                                       border border-gray-200 bg-white hover:bg-red-50 hover:border-red-300
                                       text-gray-400 hover:text-red-600 transition-colors"
                                    title="Cetak PDF">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Belum ada dokumen DPIA</span>
                                    @if ($tahunContext?->is_active)
                                        <a href="{{ route('admin.dpia.create') }}"
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                            + Buat DPIA pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($dpias->hasPages())
                <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                    {{ $dpias->withQueryString()->links() }}
                </div>
            @endif
        </div>


        {{-- MODAL EXPORT PDF --}}
        <div id="modalExportPDF" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) this.classList.add('hidden')">
            <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Export PDF — DPIA</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Pilih filter data yang akan diekspor</p>
                    </div>
                    <button onclick="document.getElementById('modalExportPDF').classList.add('hidden')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.dpia.export-pdf') }}" method="GET" target="_blank"
                    class="px-6 py-5 space-y-4">
                    @if (auth()->user()->hasRole(['admin']))
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">OPD</label>
                            <select name="opd_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                   focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Semua OPD</option>
                                @foreach ($opds as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->namaopd }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="flex items-center justify-end gap-3 pt-1">
                        <button type="button" onclick="document.getElementById('modalExportPDF').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50
                               px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                            Export PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') document.getElementById('modalExportPDF').classList.add('hidden');
                });
            </script>
        @endpush
    @endsection
