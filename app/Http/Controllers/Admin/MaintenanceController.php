<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Toggle status maintenance mode.
     * Hanya dapat diakses oleh admin (dijaga di route level).
     */
    public function toggle(Request $request)
    {
        $current = Setting::maintenanceActive();
        $newValue = !$current;

        Setting::set('maintenance_mode', $newValue ? '1' : '0');

        // Catat di audit log
        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => $newValue ? 'maintenance_enabled' : 'maintenance_disabled',
            'model_type' => 'Setting',
            'model_id'   => null,
            'new_values' => ['maintenance_mode' => $newValue],
            'ip_address' => $request->ip(),
        ]);

        $label = $newValue ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Mode maintenance berhasil {$label}.");
    }
}
