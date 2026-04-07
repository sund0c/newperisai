@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola akun admin, support, CSIRT, DPO, dan user publik')

@section('content')

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        @php
            $stats = [
                ['label' => 'Total User', 'value' => $totalAll, 'color' => 'blue'],
                ['label' => 'Aktif', 'value' => $totalActive, 'color' => 'green'],
                ['label' => 'Nonaktif', 'value' => $totalInactive, 'color' => 'yellow'],
                ['label' => 'Dihapus', 'value' => $totalDeleted, 'color' => 'red'],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold text-{{ $stat['color'] }}-600">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, email, instansi..."
                    class="w-56 px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select name="role"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>

                <select name="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            <button onclick="document.getElementById('modalTambahUser').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </button>
        </div>

        {{-- Tabel --}}
        @if ($users->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-400">Tidak ada user yang sesuai filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[800px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Instansi</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                2FA</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Login Terakhir</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            @php
                                $isDeleted = $user->trashed();
                                $isSelf = $user->id === auth()->id();
                                $roleName = $user->roles->first()?->name ?? '-';
                                $roleColors = [
                                    'admin' => 'red',
                                    'support' => 'purple',
                                    'csirt' => 'indigo',
                                    'dpo' => 'teal',
                                    'public' => 'blue',
                                ];
                                $roleColor = $roleColors[$roleName] ?? 'gray';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isDeleted ? 'opacity-50' : '' }}">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if ($isSelf)
                                            <span class="ml-1 text-xs text-blue-500 font-normal">(Anda)</span>
                                        @endif
                                    </p>
                                    @if ($user->phone)
                                        <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    {{ $user->email }}
                                    @if (!$user->email_verified_at)
                                        <span
                                            class="ml-1 px-1.5 py-0.5 bg-yellow-100 text-yellow-600 text-xs rounded">Unverified</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $user->organization ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                               bg-{{ $roleColor }}-100 text-{{ $roleColor }}-700">
                                        {{ ucfirst($roleName) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($isDeleted)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Dihapus</span>
                                    @elseif($user->is_active)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @else
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($user->hasTwoFactorEnabled())
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 1l2.928 5.978L19 8.09l-4.5 4.381L15.856 19 10 16.02 4.144 19l1.356-6.528L1 8.09l6.072-1.112L10 1z" />
                                            </svg>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">

                                        @if ($isDeleted)
                                            {{-- Restore --}}
                                            <form action="{{ route('admin.users.restore', $user) }}" method="POST"
                                                onsubmit="return confirm('Pulihkan user {{ $user->name }}?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-green-50 text-green-600 hover:bg-green-100
                                                           border border-green-200 transition-colors">
                                                    Pulihkan
                                                </button>
                                            </form>
                                        @else
                                            {{-- Reset Password --}}
                                            @if (!$isSelf)
                                                <form action="{{ route('admin.users.reset-password', $user) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Reset password {{ $user->name }}? Password baru akan dikirim ke email mereka.')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                               bg-yellow-50 text-yellow-700 hover:bg-yellow-100
                                                               border border-yellow-200 transition-colors">
                                                        Reset PW
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Toggle Aktif --}}
                                            @if (!$isSelf)
                                                <form action="{{ route('admin.users.toggle-active', $user) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user {{ $user->name }}?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                               {{ $user->is_active
                                                                   ? 'bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200'
                                                                   : 'bg-green-50 text-green-600 hover:bg-green-100 border border-green-200' }}
                                                               transition-colors">
                                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Hapus --}}
                                            @if (!$isSelf)
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                    onsubmit="return confirm('Hapus user {{ $user->name }}? Data tidak akan hilang permanen.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                               bg-red-50 text-red-600 hover:bg-red-100
                                                               border border-red-200 transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                    </div>
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

    {{-- ════ MODAL TAMBAH USER ════ --}}
    <div id="modalTambahUser" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl ring-1 ring-black/10 w-full max-w-md">

            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah User Baru</h3>
                    <p class="text-xs text-gray-500 mt-0.5">User akan menerima email untuk set password.</p>
                </div>
                <button onclick="document.getElementById('modalTambahUser').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="px-6 py-5 space-y-4">
                @csrf

                {{-- Role dulu --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="roleSelect" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="support" {{ old('role') === 'support' ? 'selected' : '' }}>Support</option>
                        <option value="csirt" {{ old('role') === 'csirt' ? 'selected' : '' }}>CSIRT</option>
                        <option value="dpo" {{ old('role') === 'dpo' ? 'selected' : '' }}>DPO</option>
                        <option value="public" {{ old('role') === 'public' ? 'selected' : '' }}>Public</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nama lengkap">
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

                {{-- Field organisasi: muncul hanya jika role=public --}}
                <div id="fieldOrganisasi" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Instansi/Organisasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="organization" id="organizationInput" value="{{ old('organization') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nama instansi pelapor">
                </div>

                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                    <strong>Info:</strong> Password tidak diset di sini. User harus klik
                    <em>Lupa Password</em> di halaman login untuk membuat password pertama mereka.
                </div>

                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalTambahUser').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Buat User
                    </button>
                </div>
            </form>

            <script>
                (function() {
                    var roleSelect = document.getElementById('roleSelect');
                    var fieldOrganisasi = document.getElementById('fieldOrganisasi');
                    var orgInput = document.getElementById('organizationInput');

                    function handleRoleChange() {
                        var isPublic = roleSelect.value === 'public';
                        fieldOrganisasi.classList.toggle('hidden', !isPublic);
                        orgInput.required = isPublic;
                        if (!isPublic) orgInput.value = '';
                    }

                    roleSelect.addEventListener('change', handleRoleChange);

                    // Inisialisasi saat modal dibuka (untuk old() value setelah validasi error)
                    handleRoleChange();
                })();
            </script>
        </div>
    </div>

    {{-- Buka modal otomatis jika ada error validasi (dari form modal) --}}
    @if ($errors->any())
        <script>
            document.getElementById('modalTambahUser').classList.remove('hidden');
        </script>
    @endif

@endsection
