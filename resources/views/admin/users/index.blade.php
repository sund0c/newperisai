@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola akun admin, auditor, verifikator dan OPD')

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
                                Email / No HP</th>
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

                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors {{ $isDeleted ? 'opacity-50' : '' }}">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if ($isSelf)
                                            <span class="ml-1 text-xs text-blue-500 font-normal">(Anda)</span>
                                        @endif
                                    </p>
                                    {{-- @if ($user->phone)
                                        <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                    @endif --}}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    {{ $user->email }}
                                    @if (!$user->email_verified_at)
                                        <span
                                            class="ml-1 px-1.5 py-0.5 bg-yellow-100 text-yellow-600 text-xs rounded">Unverified</span>
                                    @endif
                                    @if ($user->phone)
                                        <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $user->opd->namaopd ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @foreach ($user->roles as $role)
                                            @php
                                                $badgeClass = match ($role->name) {
                                                    'admin' => 'bg-red-100 text-red-700',
                                                    'verifikator' => 'bg-purple-100 text-purple-700',
                                                    'auditor' => 'bg-indigo-100 text-indigo-700',
                                                    'opd' => 'bg-teal-100 text-teal-700',
                                                    default => 'bg-gray-100 text-gray-700',
                                                };
                                                $badgeLabel = match ($role->name) {
                                                    'admin' => 'ADM',
                                                    'verifikator' => 'VER',
                                                    'auditor' => 'AUD',
                                                    'opd' => 'OPD',
                                                    default => strtoupper(substr($role->name, 0, 3)),
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex px-2 py-1 rounded text-xs font-bold tracking-widest {{ $badgeClass }}">
                                                {{ $badgeLabel }}
                                            </span>
                                        @endforeach

                                        @if ($user->roles->isEmpty())
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </div>
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
                                        <svg class="w-4 h-4 text-green-500 mx-auto" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <span class="text-gray-300 text-sm font-medium">—</span>
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
                                            {{-- Edit --}}
                                            @if (!$isSelf)
                                                <button type="button"
                                                    onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', {{ $user->opd_id ?? 'null' }}, {{ json_encode($user->roles->pluck('name')) }})"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition-colors">
                                                    Edit
                                                </button>
                                            @endif

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
                                            {{-- @if (!$isSelf)
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
                                            @endif --}}

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


    {{-- ════ MODAL EDIT USER ════ --}}
    <div id="modalEditUser" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div
                class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between sticky top-0 z-10">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Edit User</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data dan role user.</p>
                </div>
                <button onclick="document.getElementById('modalEditUser').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                       text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formEditUser" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')

                {{-- Role --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ([
            'admin' => 'Admin',
            'verifikator' => 'Verifikator',
            'auditor' => 'Auditor',
            'opd' => 'OPD',
        ] as $roleValue => $roleLabel)
                            <label
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-gray-200
                                      hover:bg-gray-50 cursor-pointer transition-all select-none
                                      has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50">
                                <input type="checkbox" name="roles[]" value="{{ $roleValue }}"
                                    class="edit-role-checkbox w-4 h-4 rounded accent-blue-600"
                                    onchange="handleEditRoleChange()">
                                <span class="text-sm text-gray-700">{{ $roleLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- OPD --}}
                <div id="fieldEditOpd" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        OPD <span class="text-red-500">*</span>
                    </label>
                    <select name="opd_id" id="editOpdSelect"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih OPD --</option>
                        @foreach ($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->namaopd }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="editName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" id="editEmail" disabled
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-400">Email tidak dapat diubah.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalEditUser').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, email, opdId, roles) {
            // Set action form
            document.getElementById('formEditUser').action = `/admin/users/${id}/update`;

            // Isi nama & email
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;

            // Set checkbox roles
            document.querySelectorAll('.edit-role-checkbox').forEach(function(cb) {
                cb.checked = roles.includes(cb.value);
            });

            // Set OPD
            var opdSelect = document.getElementById('editOpdSelect');
            opdSelect.value = opdId ?? '';
            handleEditRoleChange();

            document.getElementById('modalEditUser').classList.remove('hidden');
        }

        function handleEditRoleChange() {
            var opdChecked = document.querySelector('.edit-role-checkbox[value="opd"]').checked;
            var fieldOpd = document.getElementById('fieldEditOpd');
            var opdSelect = document.getElementById('editOpdSelect');

            fieldOpd.classList.toggle('hidden', !opdChecked);
            opdSelect.required = opdChecked;
            if (!opdChecked) opdSelect.value = '';
        }
    </script>


    {{-- ════ MODAL TAMBAH USER ════ --}}
    <div id="modalTambahUser" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div
            class="bg-white rounded-2xl border border-gray-300 shadow-2xl ring-1 ring-black/10 w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div
                class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between sticky top-0 z-10">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah User Baru</h3>
                    {{-- <p class="text-xs text-gray-500 mt-0.5">User akan menerima email untuk set password.</p> --}}
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

                {{-- Role — checkbox multi-select --}}
                <div class="grid grid-cols-2 gap-2">
                    @foreach ([
            'admin' => 'Admin',
            'verifikator' => 'Verifikator',
            'auditor' => 'Auditor',
            'opd' => 'OPD',
        ] as $roleValue => $roleLabel)
                        <label
                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-gray-200
                      hover:bg-gray-50 cursor-pointer transition-all select-none
                      has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50">
                            <input type="checkbox" name="roles[]" value="{{ $roleValue }}"
                                class="role-checkbox w-4 h-4 rounded accent-blue-600"
                                {{ in_array($roleValue, old('roles', [])) ? 'checked' : '' }}
                                onchange="handleRoleChange()">
                            <span class="text-sm text-gray-700">{{ $roleLabel }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- Dropdown OPD --}}
                <div id="fieldOpd" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        OPD <span class="text-red-500">*</span>
                    </label>
                    <select name="opd_id" id="opdSelect"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
               focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih OPD --</option>
                        @foreach ($opds as $opd)
                            <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>
                                {{ $opd->namaopd }}
                            </option>
                        @endforeach
                    </select>
                    @error('opd_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    function handleRoleChange() {
                        var opdChecked = document.querySelector('.role-checkbox[value="opd"]').checked;
                        var fieldOpd = document.getElementById('fieldOpd');
                        var opdSelect = document.getElementById('opdSelect');

                        fieldOpd.classList.toggle('hidden', !opdChecked);
                        opdSelect.required = opdChecked;
                        if (!opdChecked) opdSelect.value = '';
                    }
                    handleRoleChange();
                </script>

                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nama lengkap">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="email@domain.com">
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


        </div>
    </div>

    {{-- Buka modal otomatis jika ada error validasi (dari form modal) --}}
    @if ($errors->any())
        <script>
            document.getElementById('modalTambahUser').classList.remove('hidden');
        </script>
    @endif

@endsection
