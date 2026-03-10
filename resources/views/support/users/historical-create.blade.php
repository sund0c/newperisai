@extends('layouts.admin')

@section('page-title', 'Input Tiket Historis')
@section('page-subtitle', 'Untuk: ' . $user->name . ' — ' . $user->organization)

@section('content')

<div class="max-w-xl">

    @if($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
        @foreach($errors->all() as $error)
        <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <div class="flex items-center gap-2 mb-5 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-blue-700">
                Tiket ini akan diberi nomor <strong>BALIPROV-CSIRT-HIST-XXXXXXXX</strong>
                sebagai penanda data historis.
            </p>
        </div>

        <form method="POST"
              action="{{ route('support.users.historical.store', $user) }}"
              enctype="multipart/form-data"
              x-data="{ result: '{{ old('validation_result', '') }}' }">
            @csrf

            <div class="space-y-4">

                {{-- Judul --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Judul Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Judul singkat laporan">
                </div>

                {{-- Sistem Terdampak --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Sistem Terdampak <span class="text-gray-400">(opsional)</span>
                    </label>
                    <input type="text" name="affected_system" value="{{ old('affected_system') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="URL atau nama sistem">
                </div>

                {{-- Tanggal Laporan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Tanggal Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="reported_at" value="{{ old('reported_at') }}" required
                           max="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Dampak (pelapor) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Dampak <span class="text-red-500">*</span>
                    </label>
                    <select name="severity_reporter" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Dampak --</option>
                        @foreach(\App\Models\Report::severityLabel() as $val => $label)
                        <option value="{{ $val }}" {{ old('severity_reporter') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hasil Validasi --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Hasil Validasi <span class="text-red-500">*</span>
                    </label>
                    <select name="validation_result" required x-model="result"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Hasil --</option>
                        <option value="valid" {{ old('validation_result') === 'valid' ? 'selected' : '' }}>Valid</option>
                        <option value="invalid" {{ old('validation_result') === 'invalid' ? 'selected' : '' }}>Tidak Valid</option>
                        <option value="duplicate" {{ old('validation_result') === 'duplicate' ? 'selected' : '' }}>Duplikat</option>
                    </select>
                </div>

                {{-- Dampak Terverifikasi — hanya jika VALID --}}
                <div x-show="result === 'valid'" x-transition>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Dampak Terverifikasi <span class="text-red-500">*</span>
                    </label>
                    <select name="severity_verified"
                            :required="result === 'valid'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Dampak --</option>
                        @foreach(\App\Models\Report::severityLabel() as $val => $label)
                        <option value="{{ $val }}" {{ old('severity_verified') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Upload Sertifikat — hanya jika VALID --}}
                <div x-show="result === 'valid'" x-transition>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        e-Sertifikat PDF <span class="text-gray-400">(opsional)</span>
                    </label>
                    <input type="file" name="certificate" accept=".pdf"
                           class="w-full text-sm text-gray-600
                                  file:mr-2 file:py-1.5 file:px-3
                                  file:rounded-lg file:border file:border-gray-300
                                  file:text-xs file:font-medium file:bg-gray-50
                                  hover:file:bg-gray-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Maks. 5 MB</p>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Catatan <span class="text-gray-400">(opsional)</span>
                    </label>
                    <textarea name="admin_notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                     focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Catatan tambahan...">{{ old('admin_notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm
                                   font-semibold rounded-lg transition-colors">
                        Simpan Tiket Historis
                    </button>
                    <a href="{{ route('support.users.show', $user) }}"
                       class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm
                              font-medium rounded-lg transition-colors">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
