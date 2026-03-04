@extends('layouts.admin')

@section('title', 'Profil & Keamanan')
@section('page-title', 'Profil & Keamanan')
@section('page-subtitle', 'Kelola informasi akun dan pengaturan keamanan Anda')

@section('content')

@php $activeTab = session('active_tab', 'info') @endphp

<div class="max-w-3xl">

    {{-- Tab Navigation --}}
    <div class="flex gap-1 p-1 bg-gray-100 rounded-xl mb-6 w-fit">
        <button onclick="switchTab('info')" id="tab-btn-info"
                class="tab-btn px-4 py-2 text-sm font-medium rounded-lg transition-all">
            Informasi Akun
        </button>
        <button onclick="switchTab('password')" id="tab-btn-password"
                class="tab-btn px-4 py-2 text-sm font-medium rounded-lg transition-all">
            Ganti Password
        </button>
        <button onclick="switchTab('security')" id="tab-btn-security"
                class="tab-btn px-4 py-2 text-sm font-medium rounded-lg transition-all">
            Keamanan (2FA)
        </button>
    </div>

    {{-- ── TAB: INFORMASI AKUN ─────────────────────────────────────────── --}}
    <div id="tab-info" class="tab-content">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Informasi Akun</h2>
                <p class="text-sm text-gray-500 mt-0.5">Perbarui nama, nomor telepon, dan instansi Anda.</p>
            </div>
            <form action="{{ route('profile.update-info') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')

                {{-- Avatar --}}
                <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $user->isAdmin() ? 'bg-red-100 text-red-700' : ($user->isSupport() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($user->getRoleNames()->first() ?? 'User') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-400">Email tidak dapat diubah. Hubungi administrator jika perlu.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Instansi/Organisasi <span class="text-red-500">*</span></label>
                        <input type="text" name="organization" value="{{ old('organization', $user->organization) }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TAB: GANTI PASSWORD ─────────────────────────────────────────── --}}
    <div id="tab-password" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Ganti Password</h2>
                <p class="text-sm text-gray-500 mt-0.5">Password wajib min. 8 karakter, huruf besar/kecil, angka, dan simbol.</p>
            </div>

            {{-- Password Expiry Info --}}
            <div class="px-6 pt-4">
                @if($daysLeft <= 0)
                <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 mb-4">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    Password Anda sudah kedaluwarsa. Segera perbarui!
                </div>
                @elseif($daysLeft <= 14)
                <div class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 mb-4">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                    </svg>
                    Password kedaluwarsa {{ $daysLeft }} hari lagi ({{ $expiresAt }}).
                </div>
                @else
                <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 mb-4">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    Password aktif hingga {{ $expiresAt }} (sisa {{ $daysLeft }} hari).
                </div>
                @endif
            </div>

            <form action="{{ route('profile.update-password') }}" method="POST" class="px-6 pb-5 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full px-4 py-2.5 border @error('current_password') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                               placeholder="Password saat ini">
                        <button type="button" onclick="togglePw('current_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="new_password" required
                               class="w-full px-4 py-2.5 border @error('password') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                               placeholder="Min. 8 karakter">
                        <button type="button" onclick="togglePw('new_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Password strength indicator --}}
                    <div class="mt-2">
                        <div class="flex gap-1 mb-1">
                            <div id="str-1" class="h-1 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                            <div id="str-2" class="h-1 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                            <div id="str-3" class="h-1 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                            <div id="str-4" class="h-1 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                        </div>
                        <p id="str-label" class="text-xs text-gray-400"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confirm_password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                               placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePw('confirm_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="match-hint" class="mt-1 text-xs hidden"></p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TAB: KEAMANAN 2FA ───────────────────────────────────────────── --}}
    <div id="tab-security" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">Two-Factor Authentication (2FA)</h2>
                <p class="text-sm text-gray-500 mt-0.5">Tambahkan lapisan keamanan ekstra pada akun Anda.</p>
            </div>
            <div class="px-6 py-5">
                @if($user->google2fa_enabled)
                {{-- 2FA Aktif --}}
                <div class="flex items-start gap-4 p-4 bg-green-50 border border-green-200 rounded-xl mb-5">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-green-800">2FA Aktif</p>
                        <p class="text-xs text-green-700 mt-0.5">Akun Anda dilindungi dengan Google Authenticator.</p>
                    </div>
                </div>

                <form action="{{ route('2fa.disable') }}" method="POST" class="space-y-4">
                    @csrf
                    <p class="text-sm text-gray-600">Untuk menonaktifkan 2FA, masukkan password dan kode OTP saat ini:</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400"
                               placeholder="Password Anda">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode OTP</label>
                        <input type="text" name="otp" maxlength="6" inputmode="numeric" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-center font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-red-400"
                               placeholder="000000">
                        @error('otp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg text-sm transition-colors"
                            onclick="return confirm('Yakin ingin menonaktifkan 2FA? Keamanan akun Anda akan berkurang.')">
                        Nonaktifkan 2FA
                    </button>
                </form>

                @else
                {{-- 2FA Tidak Aktif --}}
                <div class="flex items-start gap-4 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-5">
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">2FA Belum Aktif</p>
                        <p class="text-xs text-amber-700 mt-0.5">Aktifkan 2FA untuk melindungi akun dari akses tidak sah.</p>
                    </div>
                </div>
                <a href="{{ route('2fa.setup') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Setup 2FA Sekarang
                </a>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
const tabs = ['info', 'password', 'security'];

function switchTab(name) {
    tabs.forEach(t => {
        document.getElementById('tab-' + t).classList.add('hidden');
        document.getElementById('tab-btn-' + t).classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
        document.getElementById('tab-btn-' + t).classList.add('text-gray-500');
    });
    document.getElementById('tab-' + name).classList.remove('hidden');
    document.getElementById('tab-btn-' + name).classList.add('bg-white', 'shadow-sm', 'text-gray-900');
    document.getElementById('tab-btn-' + name).classList.remove('text-gray-500');
}

// Init active tab
switchTab('{{ $activeTab }}');

// Toggle password visibility
function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Password strength meter
document.getElementById('new_password').addEventListener('input', function () {
    const val = this.value;
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
    const labels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat'];
    const labelColors = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-green-600'];

    for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('str-' + i);
        bar.className = 'h-1 flex-1 rounded-full transition-colors duration-300 ' +
            (i <= score ? colors[score - 1] : 'bg-gray-200');
    }

    const label = document.getElementById('str-label');
    if (val.length > 0) {
        label.textContent = labels[score - 1] || 'Sangat Lemah';
        label.className = 'text-xs ' + (labelColors[score - 1] || 'text-red-600');
    } else {
        label.textContent = '';
    }
});

// Password match checker
document.getElementById('confirm_password').addEventListener('input', function () {
    const newPw = document.getElementById('new_password').value;
    const hint = document.getElementById('match-hint');
    hint.classList.remove('hidden');
    if (this.value === newPw) {
        hint.textContent = '✓ Password cocok';
        hint.className = 'mt-1 text-xs text-green-600';
    } else {
        hint.textContent = '✗ Password tidak cocok';
        hint.className = 'mt-1 text-xs text-red-600';
    }
});
</script>

@endsection
