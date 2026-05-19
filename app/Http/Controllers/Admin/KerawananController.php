<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetClass;
use App\Models\AssetSubclass;
use App\Models\VulnerabilitySet;
use App\Models\VulnerabilityItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KerawananController extends Controller
{
    // =========================================================================
    // INDEX — Daftar Kelas Aset (halaman utama Master Kerawanan)
    // =========================================================================
    public function index()
    {
        $assetClasses = AssetClass::with([
            'subclasses',
            'activeVulnerabilitySet.items',
        ])
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        return view('admin.master-kerawanan.index', compact('assetClasses'));
    }

    // =========================================================================
    // SHOW CLASS — Daftar sub-kelas + kerawanan global kelas ini
    // =========================================================================
    public function showClass(AssetClass $assetClass)
    {
        $assetClass->load([
            'subclasses' => fn($q) => $q->orderBy('urutan'),
            'vulnerabilitySets' => fn($q) => $q->orderByDesc('created_at'),
        ]);

        $activeSet = $assetClass->activeVulnerabilitySet()->with('items')->first();
        $allVersions = $assetClass->vulnerabilitySets()
            ->published()
            ->orderByDesc('created_at')
            ->get();
        $draftSet = $assetClass->vulnerabilitySets()
            ->where('published_at', null)
            ->first();

        return view('admin.master-kerawanan.show-class', compact(
            'assetClass',
            'activeSet',
            'allVersions',
            'draftSet'
        ));
    }

    // =========================================================================
    // SHOW SUBCLASS — Kerawanan spesifik sub-kelas
    // =========================================================================
    public function showSubclass(AssetClass $assetClass, AssetSubclass $assetSubclass)
    {
        abort_if($assetSubclass->asset_class_id !== $assetClass->id, 404);

        $activeGlobalSet = $assetClass->activeVulnerabilitySet()->with('items')->first();
        $activeSet       = $assetSubclass->activeVulnerabilitySet()->with('items')->first();
        $allVersions     = $assetSubclass->vulnerabilitySets()
            ->published()
            ->orderByDesc('created_at')
            ->get();
        $draftSet        = $assetSubclass->vulnerabilitySets()
            ->where('published_at', null)
            ->first();

        return view('admin.master-kerawanan.show-subclass', compact(
            'assetClass',
            'assetSubclass',
            'activeGlobalSet',
            'activeSet',
            'allVersions',
            'draftSet'
        ));
    }

    // =========================================================================
    // BUAT VERSI BARU (DRAFT) — Clone dari versi aktif, atau kosong jika belum ada
    // =========================================================================
    public function createVersion(Request $request)
    {
        $request->validate([
            'scope_type' => 'required|in:global_class,subclass',
            'scope_id'   => 'required|uuid',
        ]);

        $scopeType = $request->scope_type;
        $scopeId   = $request->scope_id;

        // Cek tidak ada draft yang sedang aktif
        $existingDraft = VulnerabilitySet::where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->whereNull('published_at')
            ->first();

        if ($existingDraft) {
            return back()->with('error', 'Masih ada draft versi yang belum dipublish. Selesaikan atau hapus draft tersebut terlebih dahulu.');
        }

        $newVersi = VulnerabilitySet::nextVersion($scopeType, $scopeId);

        DB::transaction(function () use ($scopeType, $scopeId, $newVersi, $request) {
            // Buat set baru (draft)
            $newSet = VulnerabilitySet::create([
                'scope_type'        => $scopeType,
                'scope_id'          => $scopeId,
                'versi'             => $newVersi,
                'is_active'         => false,
                'catatan_perubahan' => null,
                'created_by'        => auth()->id(),
                'published_at'      => null,
            ]);

            // Clone items dari versi aktif sebelumnya
            $activeSet = VulnerabilitySet::where('scope_type', $scopeType)
                ->where('scope_id', $scopeId)
                ->where('is_active', true)
                ->with('items')
                ->first();

            if ($activeSet) {
                foreach ($activeSet->items as $item) {
                    VulnerabilityItem::create([
                        'set_id'           => $newSet->id,
                        'nomor_urut'       => $item->nomor_urut,
                        'deskripsi'        => $item->deskripsi,
                        'kontrol_tipikal' => $item->kontrol_tipikal,
                        'mitigasi_tipikal' => $item->mitigasi_tipikal,
                        'catatan_platform' => $item->catatan_platform,
                    ]);
                }
            }
        });

        return back()->with('success', "Draft versi {$newVersi} berhasil dibuat. Silakan edit item kerawanan sebelum dipublish.");
    }

    // =========================================================================
    // PUBLISH DRAFT — Set draft menjadi versi aktif
    // =========================================================================
    public function publishVersion(Request $request, VulnerabilitySet $set)
    {
        $request->validate([
            'catatan_perubahan' => 'required|string|min:10',
        ]);

        abort_if($set->isPublished(), 403, 'Versi ini sudah dipublish dan tidak dapat diubah.');
        abort_if($set->items()->count() === 0, 422, 'Tidak dapat mempublish versi tanpa item kerawanan.');

        DB::transaction(function () use ($set, $request) {
            // Non-aktifkan versi sebelumnya
            VulnerabilitySet::where('scope_type', $set->scope_type)
                ->where('scope_id', $set->scope_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Publish versi baru
            $set->update([
                'is_active'         => true,
                'catatan_perubahan' => $request->catatan_perubahan,
                'published_by'      => auth()->id(),
                'published_at'      => now(),
            ]);
        });

        return back()->with('success', "Versi {$set->versi} berhasil dipublish dan sekarang menjadi versi aktif.");
    }

    // =========================================================================
    // HAPUS DRAFT — Hanya draft (belum published) yang bisa dihapus
    // =========================================================================
    public function deleteDraft(VulnerabilitySet $set)
    {
        abort_if($set->isPublished(), 403, 'Versi yang sudah dipublish tidak dapat dihapus.');

        $set->items()->delete();
        $set->delete();

        return back()->with('success', 'Draft versi berhasil dihapus.');
    }

    // =========================================================================
    // CRUD ITEM — Tambah item ke draft set
    // =========================================================================
    public function storeItem(Request $request, VulnerabilitySet $set)
    {
        abort_if($set->isPublished(), 403, 'Set sudah dipublish, tidak dapat menambah item.');

        $validated = $request->validate([
            'deskripsi'        => 'required|string|max:1000',
            'kontrol_tipikal' => 'nullable|string|max:2000',
            'mitigasi_tipikal' => 'nullable|string|max:2000',
            'catatan_platform' => 'nullable|string|max:1000',
        ]);

        $maxNomor = $set->items()->max('nomor_urut') ?? 0;

        VulnerabilityItem::create(array_merge($validated, [
            'set_id'     => $set->id,
            'nomor_urut' => $maxNomor + 1,
        ]));

        return back()->with('success', 'Item kerawanan berhasil ditambahkan.');
    }

    // =========================================================================
    // UPDATE ITEM
    // =========================================================================
    public function updateItem(Request $request, VulnerabilityItem $item)
    {
        abort_if($item->set->isPublished(), 403, 'Set sudah dipublish, item tidak dapat diubah.');

        $validated = $request->validate([
            'deskripsi'        => 'required|string|max:1000',
            'kontrol_tipikal' => 'nullable|string|max:2000',
            'mitigasi_tipikal' => 'nullable|string|max:2000',
            'catatan_platform' => 'nullable|string|max:1000',
        ]);

        $item->update($validated);

        return back()->with('success', 'Item kerawanan berhasil diperbarui.');
    }

    // =========================================================================
    // HAPUS ITEM
    // =========================================================================
    public function destroyItem(VulnerabilityItem $item)
    {
        abort_if($item->set->isPublished(), 403, 'Set sudah dipublish, item tidak dapat dihapus.');

        $set = $item->set;
        $item->delete();

        // Re-number urutan
        $set->items()->orderBy('nomor_urut')->get()->each(function ($i, $idx) {
            $i->update(['nomor_urut' => $idx + 1]);
        });

        return back()->with('success', 'Item kerawanan berhasil dihapus.');
    }

    // =========================================================================
    // REORDER ITEMS (via drag-and-drop, AJAX)
    // =========================================================================
    public function reorderItems(Request $request, VulnerabilitySet $set)
    {
        abort_if($set->isPublished(), 403);

        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'uuid',
        ]);

        DB::transaction(function () use ($request, $set) {
            foreach ($request->order as $index => $itemId) {
                VulnerabilityItem::where('id', $itemId)
                    ->where('set_id', $set->id)
                    ->update(['nomor_urut' => $index + 1]);
            }
        });

        return response()->json(['success' => true]);
    }

    // =========================================================================
    // LIHAT VERSI HISTORIS (read-only)
    // =========================================================================
    public function showVersion(VulnerabilitySet $set)
    {
        $set->load('items', 'createdBy', 'publishedBy');

        if ($set->scope_type === 'global_class') {
            $scope = AssetClass::find($set->scope_id);
            return view('admin.master-kerawanan.show-version', compact('set', 'scope'));
        }

        $scope = AssetSubclass::with('assetClass')->find($set->scope_id);
        return view('admin.master-kerawanan.show-version', compact('set', 'scope'));
    }
}
