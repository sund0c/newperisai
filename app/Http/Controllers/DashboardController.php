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
        } elseif ($user->hasRole('opd')) {
            return view('opd.dashboard');
        } elseif ($user->hasRole('auditor')) {
            return view('auditor.dashboard');
        } elseif ($user->hasRole('verifikator')) {
            return view('verifikator.dashboard');
        } else {
            return view('opd.dashboard');
        }
    }
}
