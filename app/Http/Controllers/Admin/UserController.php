<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Opd;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'opd')->withTrashed();
        $opds = Opd::orderBy('namaopd')->get();
        if ($request->role) {
            $query->role($request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhereHas('opd', fn($q) => $q->where('namaopd', 'like', '%' . $request->search . '%'));
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
            'opds',
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
            'roles'   => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
            'opd_id'  => 'nullable|exists:opds,id|required_if:roles.*,opd',
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users',
        ]);

        $user = User::create([
            'name'              => strip_tags($validated['name']),
            'email'             => $validated['email'],
            'password'          => Hash::make(Str::random(32)),
            'opd_id'            => in_array('opd', $request->roles) ? $request->opd_id : 1,
            'email_verified_at' => now(),
            'is_active'         => true,
            'must_change_password' => false,
        ]);

        $user->syncRoles($request->roles);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_created',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'new_values' => ['email' => $user->email, 'roles' => $request->roles],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Kirim welcome email
        $user->notify(new WelcomeUserNotification($user));

        // Kirim link reset password via Password Broker
        \Illuminate\Support\Facades\Password::broker()->sendResetLink(
            ['email' => $user->email]
        );

        return back()->with('success', "User {$user->name} berhasil dibuat.");
    }

    // public function toggleActive(User $user, Request $request)
    // {
    //     if ($user->id === auth()->id()) {
    //         return back()->withErrors(['error' => 'Tidak bisa menonaktifkan akun sendiri.']);
    //     }

    //     $user->update(['is_active' => !$user->is_active]);

    //     AuditLog::create([
    //         'user_id'    => auth()->id(),
    //         'action'     => $user->is_active ? 'user_activated' : 'user_deactivated',
    //         'model_type' => 'User',
    //         'model_id'   => $user->id,
    //         'ip_address' => $request->ip(),
    //         'user_agent' => $request->userAgent(),
    //     ]);

    //     $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

    //     return back()->with('success', "User {$user->name} berhasil {$status}.");
    // }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles'   => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
            'opd_id'  => 'nullable|exists:opds,id|required_if:roles.*,opd',
            'name'    => 'required|string|max:255',
        ]);

        $user->update([
            'name'   => strip_tags($validated['name']),
            'opd_id' => in_array('opd', $request->roles) ? $request->opd_id : 1,
        ]);

        $user->syncRoles($request->roles);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_updated',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'new_values' => ['name' => $user->name, 'roles' => $request->roles],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "User {$user->name} berhasil diperbarui.");
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
