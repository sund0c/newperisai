<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PasswordHistory;
use App\Models\User;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Middleware\SandidataMiddleware;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::role('public')->with('roles');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('organization', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->withCount('reports')->latest()->paginate(20);

        return view('support.users.index', compact('users'));
    }

    public function create()
    {
        return view('support.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'organization' => 'required|string|max:255',
            'phone'        => 'nullable|string',
        ]);

        $user = DB::transaction(function () use ($request) {
            $phone = $request->phone
                ? SandidataMiddleware::encryptValue(strip_tags($request->phone))
                : null;

            $user = User::create([
                'name'                 => strip_tags($request->name),
                'email'                => $request->email,
                'password'             => Hash::make(\Str::random(32)), // password acak, tidak dikirim
                'phone'                => $phone,
                'organization'         => strip_tags($request->organization),
                'email_verified_at'    => now(),
                'is_active'            => true,
                'must_change_password' => true,
                'password_changed_at'  => null,
            ]);

            $user->assignRole('public');

            PasswordHistory::create([
                'user_id'  => $user->id,
                'password' => $user->password,
            ]);

            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'user_created_by_support',
                'model_type' => 'User',
                'model_id'   => $user->id,
                'new_values' => ['email' => $user->email, 'role' => 'public'],
                'ip_address' => $request->ip(),
            ]);

            // Kirim welcome email
            $user->notify(new WelcomeUserNotification($user));

            // Kirim link reset password via Password Broker
            \Illuminate\Support\Facades\Password::broker()->sendResetLink(
                ['email' => $user->email]
            );

            return $user;
        });

        return redirect()->route('support.users.show', $user)
            ->with('success', 'User berhasil ditambahkan dan notifikasi email telah dikirim.');
    }

    public function show(User $user)
    {
        abort_unless($user->hasRole('public'), 403);

        $totalTickets      = $user->reports()->count();
        $totalHistorical   = $user->reports()->where('is_historical', true)->count();
        $totalCertificates = $user->reports()
            ->where('is_historical', true)
            ->whereNotNull('certificate_file')
            ->count();
        $totalAll = $totalTickets;

        $reports = $user->reports()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('support.users.show', compact(
            'user',
            'reports',
            'totalTickets',
            'totalHistorical',
            'totalCertificates',
            'totalAll'
        ));
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_unless($user->hasRole('public'), 403);

        DB::transaction(function () use ($user, $request) {

            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'password_reset_by_support',
                'model_type' => 'User',
                'model_id'   => $user->id,
                'ip_address' => $request->ip(),
            ]);

            // 1. Kirim notifikasi ke user bahwa reset diminta oleh support
            $user->notify(new \App\Notifications\PasswordResetBySupportNotification($user));

            // 2. Kirim link reset via Laravel Password Broker
            \Illuminate\Support\Facades\Password::broker()->sendResetLink(
                ['email' => $user->email]
            );
        });

        return back()->with('success', 'Notifikasi dan link reset password telah dikirim ke email user.');
    }
}
