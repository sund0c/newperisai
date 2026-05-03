<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = Period::query()
            ->orderBy('tahun', 'desc');
        $periods = $query->get();
        return view('admin.periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun'     => ['required', 'integer', 'min:2025', 'max:2099', 'unique:asset_periods,tahun'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_active')) {
            Period::query()->update(['is_active' => false]);
        }

        Period::create([
            'tahun'     => $request->tahun,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Periode {$request->tahun} berhasil ditambahkan.");
    }


    public function destroy(Period $period)
    {
        // Cek apakah period sedang digunakan asset_instances
        if ($period->assetInstances()->exists()) {
            return back()->with(
                'error',
                "Periode {$period->tahun} tidak dapat dihapus karena sedang digunakan oleh data aset."
            );
        }

        $period->delete();
        return back()->with('success', "Periode {$period->tahun} berhasil dihapus.");
    }

    public function activate(Period $period)
    {
        Period::query()->update(['is_active' => false]);
        $period->update(['is_active' => true]);
        return back()->with('success', "Periode {$period->tahun} diaktifkan.");
    }
}
