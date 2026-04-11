@extends('layouts.admin')

@section('page-title', 'Manajemen User Publik')
@section('page-subtitle', 'Daftar pengguna yang terdaftar sebagai pelapor')

@section('content')

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <form method="GET" class="flex items-center gap-2 flex-1 max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, instansi..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700
                           rounded-lg text-sm font-medium transition-colors">
                    Cari
                </button>
            </form>

            <a href="{{ route('support.users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                  text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </a>
        </div>

        @if ($users->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-gray-400">Belum ada user publik terdaftar.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[640px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Instansi</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Total Laporan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Terdaftar</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    {{-- @if ($user->phone)
                        <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                        @endif --}}
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $user->organization }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $user->reports_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('support.users.show', $user) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs
                                  font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100
                                  transition-colors border border-blue-200">
                                        Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
