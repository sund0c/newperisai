<?php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', '2fa', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::with('roles')->withTrashed();

        if ($request->role) {
            $query->role($request->role);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(20);
        $roles  = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    // Buat user support/admin baru (by admin)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'organization' => 'required|string|max:255',
            'role'         => 'required|in:support,admin',
            'password'     => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ]);

        $user = User::create([
            'name'              => strip_tags($validated['name']),
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'organization'      => strip_tags($validated['organization']),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        $user->assignRole($validated['role']);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_created',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'new_values' => ['email' => $user->email, 'role' => $validated['role']],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "User {$user->name} berhasil dibuat.");
    }

    // Toggle aktif/nonaktif user
    public function toggleActive(User $user, Request $request)
    {
        // Admin tidak bisa nonaktifkan diri sendiri
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
        ]);

        return back()->with('success', 'Status user berhasil diubah.');
    }

    // Soft delete user
    public function destroy(User $user, Request $request)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $user->delete(); // soft delete

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'user_deleted',
            'model_type' => 'User',
            'model_id'   => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'User berhasil dihapus.');
    }
}
