<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeVersion;
use App\Models\SeIndikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeIndikatorController extends Controller
{
    public function store(Request $request, SeVersion $seVersion)
    {
        $validated = $request->validate([
            'urutan'     => 'required|integer|min:1|max:99',
            'pertanyaan' => 'required|string|max:1000',
            'keterangan' => 'nullable|string|max:500',
            'pilihan_1'  => 'required|string|max:255',
            'pilihan_2'  => 'required|string|max:255',
            'pilihan_3'  => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($seVersion, $validated) {
            // Geser ke bawah order DESC untuk hindari unique collision
            SeIndikator::where('se_version_id', $seVersion->id)
                ->where('urutan', '>=', $validated['urutan'])
                ->orderBy('urutan', 'desc')
                ->get()
                ->each(fn($ind) => $ind->update(['urutan' => $ind->urutan + 1]));

            SeIndikator::create(array_merge($validated, [
                'se_version_id' => $seVersion->id,
            ]));
        });

        return redirect()->route('admin.master-se.show', $seVersion)
            ->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function update(Request $request, SeVersion $seVersion, SeIndikator $indikator)
    {
        $validated = $request->validate([
            'urutan'     => [
                'required',
                'integer',
                'min:1',
                'max:99',
                // Urutan unik per versi, kecuali milik indikator ini sendiri
                \Illuminate\Validation\Rule::unique('se_indikators')
                    ->where('se_version_id', $seVersion->id)
                    ->whereNull('deleted_at')
                    ->ignore($indikator->id),
            ],
            'pertanyaan' => 'required|string|max:1000',
            'keterangan' => 'nullable|string|max:500',
            'pilihan_1'  => 'required|string|max:255',
            'pilihan_2'  => 'required|string|max:255',
            'pilihan_3'  => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($seVersion, $indikator, $validated) {
            $urutanLama = $indikator->urutan;
            $urutanBaru = $validated['urutan'];

            if ($urutanLama !== $urutanBaru) {
                if ($urutanBaru > $urutanLama) {
                    // Geser ke atas: yang di antara lama+1 s/d baru → turun 1 (ASC)
                    SeIndikator::where('se_version_id', $seVersion->id)
                        ->whereBetween('urutan', [$urutanLama + 1, $urutanBaru])
                        ->orderBy('urutan', 'asc')
                        ->get()
                        ->each(fn($ind) => $ind->update(['urutan' => $ind->urutan - 1]));
                } else {
                    // Geser ke bawah: yang di antara baru s/d lama-1 → naik 1 (DESC)
                    SeIndikator::where('se_version_id', $seVersion->id)
                        ->whereBetween('urutan', [$urutanBaru, $urutanLama - 1])
                        ->orderBy('urutan', 'desc')
                        ->get()
                        ->each(fn($ind) => $ind->update(['urutan' => $ind->urutan + 1]));
                }
            }

            $indikator->update($validated);
        });

        return redirect()->route('admin.master-se.show', $seVersion)
            ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function destroy(SeVersion $seVersion, SeIndikator $indikator)
    {
        DB::transaction(function () use ($seVersion, $indikator) {
            $urutan = $indikator->urutan;
            $indikator->delete();

            // Geser ke atas order ASC untuk hindari unique collision
            SeIndikator::where('se_version_id', $seVersion->id)
                ->where('urutan', '>', $urutan)
                ->orderBy('urutan', 'asc')
                ->get()
                ->each(fn($ind) => $ind->update(['urutan' => $ind->urutan - 1]));
        });

        return redirect()->route('admin.master-se.show', $seVersion)
            ->with('success', 'Indikator berhasil dihapus.');
    }
}
