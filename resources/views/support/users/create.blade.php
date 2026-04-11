@extends('layouts.admin')

@section('page-title', 'Tambah User Publik')
@section('page-subtitle', 'Tambahkan pelapor baru secara manual')

@section('content')

    <div class="max-w-2xl">

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Data User</h3>

            <form method="POST" action="{{ route('support.users.store') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nama sesuai KTP">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="email@domain.com">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Instansi/Organisasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="organization" value="{{ old('organization') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nama instansi atau organisasi">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">No. Telepon</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                        <strong>Informasi Password:</strong>
                        Notifikasi email akan dikirimkan otomatis ke alamat email di atas untuk segera memasukkan password
                        baru.
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm
                                   font-semibold rounded-lg transition-colors">
                            Tambah & Kirim Notifikasi
                        </button>
                        <a href="{{ route('support.users.index') }}"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm
                              font-medium rounded-lg transition-colors">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Upload Sertifikat Lama (setelah user dibuat, dari halaman index atau show) --}}
        {{-- Panel ini muncul hanya jika ada user yang baru dibuat dari session flash --}}
        @if (session('created_user_id'))
            @php $createdUser = \App\Models\User::with('reports')->find(session('created_user_id')) @endphp
            @if ($createdUser && $createdUser->reports->isNotEmpty())
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Upload Sertifikat Lama</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Unggah sertifikat yang pernah diterima user untuk laporan-laporan berikut.
                    </p>

                    @foreach ($createdUser->reports as $report)
                        <form method="POST" action="{{ route('support.users.certificate', $createdUser) }}"
                            enctype="multipart/form-data" class="mb-3 p-3 border border-gray-200 rounded-lg">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ $report->id }}">
                            <p class="text-xs font-medium text-gray-700 mb-2">
                                {{ $report->ticket_number }} — {{ Str::limit($report->title, 50) }}
                            </p>
                            <div class="flex items-center gap-2">
                                <input type="file" name="certificate" accept=".pdf" required
                                    class="text-xs text-gray-600 file:mr-2 file:py-1 file:px-3
                              file:rounded-lg file:border file:border-gray-300
                              file:text-xs file:font-medium file:bg-gray-50
                              hover:file:bg-gray-100">
                                <button type="submit"
                                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white
                               text-xs font-semibold rounded-lg transition-colors shrink-0">
                                    Upload
                                </button>
                            </div>
                        </form>
                    @endforeach
                </div>
            @endif
        @endif

    </div>

@endsection
