<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return view('admin.dashboard', [
                'maintenanceActive' => Setting::maintenanceActive(),
            ]);
        } elseif ($user->hasRole('support')) {
            return view('support.dashboard');
        } elseif ($user->hasRole('csirt')) {
            return view('csirt.dashboard');
        } elseif ($user->hasRole('dpo')) {
            return view('dpo.dashboard');
        } else {
            return view('public.dashboard');
        }
    }
}
