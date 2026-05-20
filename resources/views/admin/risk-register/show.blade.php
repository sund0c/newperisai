{{-- resources/views/admin/risk-register/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Risk Register — ' . $riskRegister->kode_rr)
@section('page-title', $riskRegister->kode_rr)
@section('page-subtitle', ($riskRegister->asset->nama_aset ?? '-') . ' — ' . ($riskRegister->opd->namaopd ?? '-'))

@section('content')

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Action bar --}}
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            @if($riskRegister->isFinal())
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Final
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Draft
                </span>
            @endif
            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">v{{ $riskRegister->versi }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if($riskRegister->isFinal())
            <form method="POST" action="{{ route('admin.risk-register.revisi', $riskRegister) }}"
                  onsubmit="return confirm('Buat revisi baru? Semua item akan disalin ke draft versi baru.')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Buat Revisi (v{{ $riskRegister->versi + 1 }})
                </button>
            </form>
            @endif
            @if($riskRegister->isDraft())
            <a href="{{ route('admin.risk-register.edit', $riskRegister) }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-amber-300 text-amber-700 text-sm font-medium rounded-lg hover:bg-amber-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Draft
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-3 gap-5">

        {{-- Kolom utama --}}
        <div class="col-span-2 space-y-5">

            {{-- Info header --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Risk Register</h3>
                <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Kode RR</div>
                        <div class="font-mono font-semibold text-blue-700">{{ $riskRegister->kode_rr }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Versi</div>
                        <div class="font-semibold">v{{ $riskRegister->versi }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Aset</div>
                        <div>{{ $riskRegister->asset->nama_aset ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Kode Aset</div>
                        <div class="font-mono text-xs">{{ $riskRegister->asset->kode_aset ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">OPD</div>
                        <div>{{ $riskRegister->opd->namaopd ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Tahun</div>
                        <div>{{ $riskRegister->tahunaktif->tahun ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Dibuat Oleh</div>
                        <div>{{ $riskRegister->dibuatOleh->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Tanggal Dibuat</div>
                        <div>{{ $riskRegister->created_at->format('d M Y') }}</div>
                    </div>
                    @if($riskRegister->isFinal())
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Difinalisasi Oleh</div>
                        <div>{{ $riskRegister->difinalisasiOleh->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-0.5">Tanggal Final</div>
                        <div>{{ $riskRegister->difinalisasi_at?->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                    @endif
                    @if($riskRegister->keterangan)
                    <div class="col-span-2">
                        <div class="text-xs text-gray-400 mb-0.5">Keterangan</div>
                        <div>{{ $riskRegister->keterangan }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Statistik --}}
            @php
                $items       = $riskRegister->items;
                $totalItems  = $items->count();
                $levelGroups = $items->groupBy('inherent_level');
            @endphp
            @if($totalItems > 0)
            <div class="grid grid-cols-5 gap-3">
                @foreach([
                    ['Sangat Tinggi', 'bg-red-50 border-red-200 text-red-700'],
                    ['Tinggi',        'bg-orange-50 border-orange-200 text-orange-700'],
                    ['Sedang',        'bg-yellow-50 border-yellow-200 text-yellow-700'],
                    ['Rendah',        'bg-blue-50 border-blue-200 text-blue-700'],
                    ['Sangat Rendah', 'bg-green-50 border-green-200 text-green-700'],
                ] as [$lvl, $cls])
                <div class="border rounded-lg p-3 {{ $cls }} text-center">
                    <div class="text-2xl font-bold">{{ ($levelGroups[$lvl] ?? collect())->count() }}</div>
                    <div class="text-xs font-medium mt-0.5">{{ $lvl }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Daftar item --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Daftar Item Risiko
                        <span class="ml-1 text-xs font-normal text-gray-400">({{ $totalItems }} item)</span>
                    </h3>
                </div>

                @if($totalItems > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($items as $item)
                    @php
                        $lc = match($item->inherent_level) {
                            'Sangat Tinggi' => 'border-l-red-500',
                            'Tinggi'        => 'border-l-orange-500',
                            'Sedang'        => 'border-l-yellow-500',
                            'Rendah'        => 'border-l-blue-500',
                            default         => 'border-l-green-500',
                        };
                        $badge = match($item->inherent_level) {
                            'Sangat Tinggi' => 'bg-red-100 text-red-800',
                            'Tinggi'        => 'bg-orange-100 text-orange-800',
                            'Sedang'        => 'bg-yellow-100 text-yellow-800',
                            'Rendah'        => 'bg-blue-100 text-blue-800',
                            default         => 'bg-green-100 text-green-800',
                        };
                    @endphp
                    <div class="border-l-4 {{ $lc }} px-5 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono text-gray-400">#{{ $item->risk_no }}</span>
                                    @if($item->jenis_risiko)
                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $item->jenis_risiko }}</span>
                                    @endif
                                    @if($item->kategori)
                                    <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-xs rounded">{{ $item->kategori }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ancaman</span>
                                    <p class="text-sm text-gray-800 mt-0.5">{{ $item->ancaman }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kerawanan</span>
                                    <p class="text-sm text-gray-700 mt-0.5">{{ $item->kerawanan }}</p>
                                </div>
                                @if($item->dampak_detail)
                                <div>
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Dampak</span>
                                    <p class="text-sm text-gray-700 mt-0.5">{{ $item->dampak_detail }}</p>
                                </div>
                                @endif
                                @if($item->area_dampak)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->area_dampak as $area)
                                    <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 text-xs rounded border border-blue-100">{{ $area }}</span>
                                    @endforeach
                                </div>
                                @endif
                                @if($item->kontrol_saat_ini || $item->rencana_aksi)
                                <div class="pt-2 border-t border-gray-100 grid grid-cols-2 gap-4 text-sm">
                                    @if($item->kontrol_saat_ini)
                                    <div>
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kontrol Saat Ini</span>
                                        <p class="text-gray-700 mt-0.5">{{ $item->kontrol_saat_ini }}</p>
                                    </div>
                                    @endif
                                    @if($item->rencana_aksi)
                                    <div>
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Rencana Aksi</span>
                                        <p class="text-gray-700 mt-0.5">{{ $item->rencana_aksi }}</p>
                                    </div>
                                    @endif
                                </div>
                                @endif
                                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400">
                                    @if($item->keputusan_penanganan)
                                    <span>Keputusan: <strong class="text-gray-600">{{ $item->keputusan_penanganan }}</strong></span>
                                    @endif
                                    @if($item->prioritas_risiko)
                                    <span>Prioritas: <strong class="text-gray-600">{{ $item->prioritas_risiko }}</strong></span>
                                    @endif
                                    @if($item->target_jadwal)
                                    <span>Target: <strong class="text-gray-600">{{ $item->target_jadwal }}</strong></span>
                                    @endif
                                    @if($item->risk_owner)
                                    <span>Owner: <strong class="text-gray-600">{{ $item->risk_owner }}</strong></span>
                                    @endif
                                    @if($item->ada_residual_risk)
                                    <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded font-medium">
                                        Residual: {{ $item->residual_level }} ({{ $item->residual_skor }}) — {{ $item->residual_status }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-center">
                                <div class="px-3 py-2 rounded-lg {{ $badge }}">
                                    <div class="text-2xl font-bold">{{ $item->inherent_skor }}</div>
                                    <div class="text-xs font-medium">{{ $item->inherent_level }}</div>
                                    <div class="text-xs opacity-70 mt-0.5">D{{ $item->inherent_dampak }}×K{{ $item->inherent_kemungkinan }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-10 text-center text-gray-400 text-sm">Belum ada item risiko.</div>
                @endif
            </div>

        </div>

        {{-- Sidebar: Version history --}}
        <div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Riwayat Versi</h3>
                <div class="space-y-2">
                    @foreach($versions as $v)
                    <a href="{{ route('admin.risk-register.show', $v) }}"
                       class="flex items-center justify-between p-2.5 rounded-lg border text-sm transition
                              {{ $v->id === $riskRegister->id
                                  ? 'border-blue-300 bg-blue-50'
                                  : 'border-gray-200 hover:bg-gray-50' }}">
                        <div>
                            <div class="font-mono text-xs font-semibold {{ $v->id === $riskRegister->id ? 'text-blue-700' : 'text-gray-700' }}">
                                {{ $v->kode_rr }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $v->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-gray-600">v{{ $v->versi }}</span>
                            @if($v->status === 'final')
                                <span class="w-2 h-2 bg-green-500 rounded-full" title="Final"></span>
                            @else
                                <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse" title="Draft"></span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

@endsection
