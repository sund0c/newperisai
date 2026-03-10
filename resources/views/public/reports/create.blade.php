@extends('layouts.admin')

@section('title', 'Buat Laporan Baru')
@section('page-title', 'Buat Laporan Baru')
@section('page-subtitle', 'Laporkan insiden atau kerentanan keamanan siber kepada CSIRT Provinsi Bali')

@section('content')

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-blue-50">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Panduan Pengisian Laporan</p>
                    <p class="text-xs text-blue-700 mt-0.5">
                        Laporan wajib disertai video Proof of Concept (PoC)
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('public.reports.store') }}" method="POST" enctype="multipart/form-data"
               class="px-6 py-5 pb-6 space-y-5">
            @csrf

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Judul Laporan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                       class="w-full px-4 py-2.5 border @error('title') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: SQL Injection pada portal dinas-xxx.baliprov.go.id">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Sistem yang Terdampak --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Sistem Elekronik Pemprov Bali yang Terdampak
                    <span class="ml-1 px-1.5 py-0.5 bg-blue-100 text-blue-600 text-xs rounded font-medium">Opsional</span>
                </label>


                <input type="text" name="affected_system" value="{{ old('affected_system') }}" maxlength="255"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="https://example.baliprov.go.id/halaman-terdampak">
                <p class="mt-1 text-xs text-gray-400">Opsional. Isi jika ada URL spesifik yang terdampak.</p>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Deskripsi Singkat Laporan <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="6" required
                          class="w-full px-4 py-2.5 border @error('description') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          placeholder="Jelaskan laporan Anda secara umum.">{{ old('description') }}</textarea>
                <div class="flex justify-between mt-1">
                    @error('description')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                    @else
                    <p class="text-xs text-gray-400">Minimal 50 karakter.</p>
                    @enderror
                    <p class="text-xs text-gray-400" id="desc-count">0 karakter</p>
                </div>
            </div>

            {{-- Severity (bahasa awam) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Seberapa besar dampak yang Anda perkirakan? <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-400 mb-3">Ini tidak mempengaruhi kualitas laporan Anda. Kami tetap akan melakukan verifikasi ulang sesuai dengan video PoC Anda.</p>
                <div class="space-y-2">
                   {{-- Sangat Berbahaya --}}
<label class="relative cursor-pointer block">
    <input type="radio" name="severity" value="critical"
           {{ old('severity') == 'critical' ? 'checked' : '' }}
           class="sr-only peer" required>
    <div class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
         border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-gray-800">Sangat Berbahaya</p>
            <p class="text-xs text-gray-500 mt-0.5">Data sensitif bisa dicuri, sistem bisa dikendalikan penuh, atau layanan publik bisa lumpuh</p>
        </div>
    </div>
</label>

{{-- Berbahaya --}}
<label class="relative cursor-pointer block">
    <input type="radio" name="severity" value="high"
           {{ old('severity') == 'high' ? 'checked' : '' }}
           class="sr-only peer" required>
    <div class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
         border-gray-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
        <svg class="w-5 h-5 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-gray-800">Berbahaya</p>
            <p class="text-xs text-gray-500 mt-0.5">Sebagian data bisa diakses tanpa izin atau fungsi sistem penting bisa terganggu</p>
        </div>
    </div>
</label>

{{-- Cukup Berbahaya --}}
<label class="relative cursor-pointer block">
    <input type="radio" name="severity" value="medium"
           {{ old('severity') == 'medium' ? 'checked' : '' }}
           class="sr-only peer" required>
    <div class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
         border-gray-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:border-yellow-300">
        <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-gray-800">Cukup Berbahaya</p>
            <p class="text-xs text-gray-500 mt-0.5">Ada celah keamanan yang bisa disalahgunakan, namun dampaknya terbatas</p>
        </div>
    </div>
</label>

{{-- Perlu Diperhatikan --}}
<label class="relative cursor-pointer block">
    <input type="radio" name="severity" value="low"
           {{ old('severity') == 'low' ? 'checked' : '' }}
           class="sr-only peer" required>
    <div class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
         border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
        <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-gray-800">Perlu Diperhatikan</p>
            <p class="text-xs text-gray-500 mt-0.5">Temuan minor yang belum berdampak langsung, namun sebaiknya diperbaiki</p>
        </div>
    </div>
</label>
                </div>
                @error('severity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- PoC Section --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-sm font-semibold text-gray-700 mb-1">Proof of Concept (PoC)</p>
                <p class="text-xs text-gray-500 mb-4">
                    Laporan akan diproses lebih lanjut jika PoC dinyatakan valid.
                    PoC yang valid adalah jika link memang berisikan video PoC yang dapat diakses dan video dapat ditonton dengan baik, serta dapat diujicoba kembali dengan langkah yang persis sama dalam video PoC dan menghasilkan hasil yang sama dengan temuan laporan.
                    Untuk itu video PoC harus menunjukkan langkah detil dan jelas. Video PoC tidak boleh dihapus sebelum proses dinyatakan Selesai.
                </p>

                {{-- Link Video --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Link Video PoC <span class="text-red-500">*</span>
                    </label>
                    <input type="url" name="poc_video_url" value="{{ old('poc_video_url') }}" required
                           class="w-full px-4 py-2.5 border @error('poc_video_url') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="https://youtube.com/... atau https://drive.google.com/...">
                    <p class="mt-1 text-xs text-gray-400">YouTube, Google Drive, atau platform video lainnya.</p>
                    @error('poc_video_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>




            </div>

            {{-- Submit --}}
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('public.reports.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    ← Kembali
                </a>
                <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateLabel(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const size = (file.size / 1024 / 1024).toFixed(2);
        label.textContent = `${file.name} (${size} MB)`;
        label.classList.add('text-blue-600', 'font-medium');
    }
}

function handleImages(input) {
    const label = document.getElementById('images-label');
    const preview = document.getElementById('image-preview');
    const files = Array.from(input.files);

    if (files.length > 3) {
        alert('Maksimal 3 gambar yang dapat diunggah.');
        input.value = '';
        return;
    }

    label.textContent = `${files.length} gambar dipilih`;
    label.classList.add('text-blue-600', 'font-medium');

    preview.innerHTML = '';
    preview.classList.remove('hidden');

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative rounded-lg overflow-hidden bg-gray-100 aspect-square';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-2 py-1">
                    <p class="text-white text-xs truncate">${file.name}</p>
                </div>`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

const desc = document.querySelector('textarea[name="description"]');
const counter = document.getElementById('desc-count');
if (desc && counter) {
    desc.addEventListener('input', () => {
        counter.textContent = desc.value.length + ' karakter';
        counter.className = desc.value.length >= 50 ? 'text-xs text-green-600' : 'text-xs text-red-500';
    });
}
</script>

@endsection
