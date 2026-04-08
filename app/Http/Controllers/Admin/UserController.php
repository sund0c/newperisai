<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->withTrashed();

        if ($request->role) {
            $query->role($request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('organization', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status === 'active') {
            $query->where('is_active', true)->whereNull('deleted_at');
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false)->whereNull('deleted_at');
        } elseif ($request->status === 'deleted') {
            $query->onlyTrashed();
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles  = Role::orderBy('name')->get();

        // Statistik ringkas
        $totalAll      = User::withTrashed()->count();
        $totalActive   = User::where('is_active', true)->count();
        $totalInactive = User::where('is_active', false)->count();
        $totalDeleted  = User::onlyTrashed()->count();

        return view('admin.users.index', compact(
            'users',
            'roles',
            'totalAll',
            'totalActive',
            'totalInactive',
            'totalDeleted'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'role'         => 'required|in:support,admin,csirt,dpo,public',
            'organization' => 'required_if:role,public|nullable|string|max:255',
        ]);

        // Role non-public: organisasi otomatis CSIRT Provinsi Bali
        $organization = $validated['role'] === 'public'
            ? strip_tags($validated['organization'])
            : 'CSIRT Provinsi Bali';

        // Password placeholder acak — user harus pakai "Lupa Password" untuk set password sendiri
        $user = User::create([
            'name'                 => strip_tags($validated['name']),
            'email'                => $validated['email'],
            'password'             => Hash::make(Str::random(32)),
            'organization'         => $organization,
            'email_verified_at'    => now(),
            'is_active'            => true,
            'must_change_password' => true,
        ]);

        $user->assignRole($validated['role']);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_created',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'new_values' => ['email' => $user->email, 'role' => $validated['role']],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "User {$user->name} berhasil dibuat dengan role {$validated['role']}.");
    }

    public function toggleActive(User $user, Request $request)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menonaktifkan akun sendiri.']);
        }

        $user->update(['is_active' => !$user->is_active]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => $user->is_active ? 'user_activated' : 'user_deactivated',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }

    public function resetPassword(User $user, Request $request)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Gunakan menu Profil untuk mengubah password sendiri.']);
        }

        // Kirim reset link via Laravel Password Broker.
        // Password TIDAK diubah di sini — user yang set sendiri via link.
        // Token di-hash bcrypt, disimpan di password_reset_tokens, expired 15 menit
        // (sesuai config/auth.php → passwords.users.expire = 15)
        $status = \Illuminate\Support\Facades\Password::broker()->sendResetLink(
            ['email' => $user->email]
        );

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_password_reset_requested',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'new_values' => ['target_email' => $user->email, 'status' => $status],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                "Link reset password telah dikirim ke {$user->email}. Link berlaku 15 menit."
            );
        }

        return back()->withErrors(['error' => __($status)]);
    }

    public function destroy(User $user, Request $request)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $userName = $user->name;
        $user->delete(); // soft delete

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_deleted',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "User {$userName} berhasil dihapus.");
    }

    public function restore(User $user, Request $request)
    {
        $user->restore();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_restored',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "User {$user->name} berhasil dipulihkan.");
    }
}
