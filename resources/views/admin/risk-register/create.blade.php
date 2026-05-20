{{-- resources/views/admin/risk-register/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Buat Risk Register')
@section('page-title', 'Buat Risk Register')
@section('page-subtitle', 'Pilih aset yang akan dibuatkan Risk Register')

@section('content')

    @if($errors->any())
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

            <form method="POST" action="{{ route('admin.risk-register.store') }}" x-data="{ selected: null }">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Aset <span class="text-red-500">*</span>
                    </label>
                    <select name="asset_id" required
                            @change="selected = $event.target.selectedOptions[0]?.dataset"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Aset --</option>
                        @foreach($assets as $asset)
                        <option value="{{ $asset->id }}"
                                data-opd="{{ $asset->opd->namaopd ?? '-' }}"
                                data-kode="{{ $asset->kode_aset ?? '-' }}"
                                data-klas="{{ $asset->subKlasifikasi->klasifikasi->klasifikasiaset ?? '-' }}"
                                data-subklas="{{ $asset->subKlasifikasi->nama ?? '-' }}"
                                @selected(old('asset_id') === $asset->id)>
                            [{{ $asset->kode_aset }}] {{ $asset->nama_aset }} — {{ $asset->opd->namaopd ?? '' }}
                        </option>
                        @endforeach
                    </select>

                    <div x-show="selected?.opd" x-cloak
                         class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm space-y-1">
                        <div class="flex gap-2"><span class="text-gray-500 w-24">OPD</span><span class="font-medium text-gray-800" x-text="selected?.opd"></span></div>
                        <div class="flex gap-2"><span class="text-gray-500 w-24">Kode Aset</span><span class="font-mono font-medium text-gray-800" x-text="selected?.kode"></span></div>
                        <div class="flex gap-2"><span class="text-gray-500 w-24">Klasifikasi</span><span class="text-gray-800" x-text="selected?.klas"></span></div>
                        <div class="flex gap-2"><span class="text-gray-500 w-24">Sub-Kelas</span><span class="text-gray-800" x-text="selected?.subklas"></span></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="keterangan" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.risk-register.index') }}"
                       class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        Buat & Lanjutkan
                    </button>
                </div>
            </form>

        </div>
    </div>

@endsection
