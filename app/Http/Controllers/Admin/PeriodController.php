<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JenisPeriode;
use App\Http\Controllers\Controller;
use App\Models\AssetPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = AssetPeriod::query()
            ->orderBy('jenis_periode')
            ->orderBy('tanggal_mulai', 'desc')
            ->get()
            ->groupBy('jenis_periode');

        return view('admin.periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode'    => ['required', 'string', 'max:100'],
            'jenis_periode'   => ['required', Rule::enum(JenisPeriode::class)],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
        ]);

        if (AssetPeriod::hasOverlap(
            $request->jenis_periode,
            $request->tanggal_mulai,
            $request->tanggal_selesai
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal_mulai' => 'Periode overlap dengan periode '
                        . JenisPeriode::from($request->jenis_periode)->label()
                        . ' yang sudah ada.',
                ]);
        }

        AssetPeriod::create($request->only([
            'nama_periode',
            'jenis_periode',
            'tanggal_mulai',
            'tanggal_selesai',
        ]));

        return redirect()
            ->route('admin.periods.index')
            ->with('success', 'Periode berhasil ditambahkan.');
    }

    public function update(Request $request, AssetPeriod $period)
    {
        $request->validate([
            'nama_periode'    => ['required', 'string', 'max:100'],
            'jenis_periode'   => ['required', Rule::enum(JenisPeriode::class)],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
        ]);

        if (AssetPeriod::hasOverlap(
            $request->jenis_periode,
            $request->tanggal_mulai,
            $request->tanggal_selesai,
            $period->id
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'tanggal_mulai' => 'Periode overlap dengan periode '
                        . JenisPeriode::from($request->jenis_periode)->label()
                        . ' yang sudah ada.',
                ]);
        }

        $period->update($request->only([
            'nama_periode',
            'jenis_periode',
            'tanggal_mulai',
            'tanggal_selesai',
        ]));

        return redirect()
            ->route('admin.periods.index')
            ->with('success', 'Periode berhasil diperbarui.');
    }

    public function activate(AssetPeriod $period)
    {
        $period->activate();

        return redirect()
            ->route('admin.periods.index')
            ->with('success', "Periode \"{$period->nama_periode}\" berhasil diaktifkan.");
    }

    public function deactivate(AssetPeriod $period)
    {
        $period->deactivate();

        return redirect()
            ->route('admin.periods.index')
            ->with('success', "Periode \"{$period->nama_periode}\" berhasil dinonaktifkan.");
    }

    public function destroy(AssetPeriod $period)
    {
        if ($period->is_active) {
            return back()->withErrors([
                'period' => 'Periode aktif tidak dapat dihapus. Nonaktifkan terlebih dahulu.',
            ]);
        }

        $period->delete();

        return redirect()
            ->route('admin.periods.index')
            ->with('success', 'Periode berhasil dihapus.');
    }
}
