<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\VulnerabilitySet;
use App\Models\VulnerabilityItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KerawananController extends Controller
{
    // =========================================================================
    // INDEX — Daftar Kelas Aset
    // =========================================================================
    public function index()
    {
        $klasifikasiAsets = DB::table('klasifikasi_asets')
            ->whereNull('deleted_at')
            ->orderBy('kodeklas')
            ->get();

        // Attach active vulnerability set count per kelas
        foreach ($klasifikasiAsets as $klas) {
            $activeSet = VulnerabilitySet::where('scope_type', 'global_class')
                ->where('scope_id', $klas->id)
                ->where('is_active', true)
                ->withCount(['items'])
                ->first();
            $klas->activeSet    = $activeSet;
            $klas->subklasCount = DB::table('sub_klasifikasi_asets')
                ->where('klasifikasi_aset_id', $klas->id)
                ->whereNull('deleted_at')
                ->count();
        }

        return view('admin.master-kerawanan.index', compact('klasifikasiAsets'));
    }

    // =========================================================================
    // SHOW CLASS — Detail kelas aset + sub-kelas + kerawanan global
    // =========================================================================
    public function showClass(string $klasId)
    {
        $klas = DB::table('klasifikasi_asets')->where('id', $klasId)->first();
        abort_if(!$klas, 404);

        $subklasifikasiAsets = DB::table('sub_klasifikasi_asets')
            ->where('klasifikasi_aset_id', $klasId)
            ->whereNull('deleted_at')
            ->get();

        // Attach active set info per sub-klas
        foreach ($subklasifikasiAsets as $sub) {
            $subActiveSet = VulnerabilitySet::where('scope_type', 'subclass')
                ->where('scope_id', $sub->id)
                ->where('is_active', true)
                ->first();
            $sub->activeSet  = $subActiveSet;
            $sub->itemCount  = $subActiveSet ? $subActiveSet->items()->count() : 0;
        }

        $activeSet   = VulnerabilitySet::where('scope_type', 'global_class')
            ->where('scope_id', $klasId)
            ->where('is_active', true)
            ->with('items')
            ->first();

        $allVersions = VulnerabilitySet::where('scope_type', 'global_class')
            ->where('scope_id', $klasId)
            ->published()
            ->orderByDesc('created_at')
            ->get();

        $draftSet    = VulnerabilitySet::where('scope_type', 'global_class')
            ->where('scope_id', $klasId)
            ->whereNull('published_at')
            ->with('items')
            ->first();

        return view('admin.master-kerawanan.show-class', compact(
            'klas',
            'subklasifikasiAsets',
            'activeSet',
            'allVersions',
            'draftSet'
        ));
    }

    // =========================================================================
    // SHOW SUBCLASS — Kerawanan spesifik sub-kelas
    // =========================================================================
    public function showSubclass(string $klasId, string $subklasId)
    {
        $klas    = DB::table('klasifikasi_asets')->where('id', $klasId)->first();
        $subklas = DB::table('sub_klasifikasi_asets')
            ->where('id', $subklasId)
            ->where('klasifikasi_aset_id', $klasId)
            ->first();
        abort_if(!$klas || !$subklas, 404);

        $activeGlobalSet = VulnerabilitySet::where('scope_type', 'global_class')
            ->where('scope_id', $klasId)
            ->where('is_active', true)
            ->with('items')
            ->first();

        $activeSet  = VulnerabilitySet::where('scope_type', 'subclass')
            ->where('scope_id', $subklasId)
            ->where('is_active', true)
            ->with('items')
            ->first();

        $allVersions = VulnerabilitySet::where('scope_type', 'subclass')
            ->where('scope_id', $subklasId)
            ->published()
            ->orderByDesc('created_at')
            ->get();

        $draftSet   = VulnerabilitySet::where('scope_type', 'subclass')
            ->where('scope_id', $subklasId)
            ->whereNull('published_at')
            ->with('items')
            ->first();

        return view('admin.master-kerawanan.show-subclass', compact(
            'klas',
            'subklas',
            'activeGlobalSet',
            'activeSet',
            'allVersions',
            'draftSet'
        ));
    }

    // =========================================================================
    // BUAT VERSI BARU (DRAFT)
    // =========================================================================
    public function createVersion(Request $request)
    {
        $request->validate([
            'scope_type' => 'required|in:global_class,subclass',
            'scope_id'   => 'required|uuid',
        ]);

        $scopeType = $request->scope_type;
        $scopeId   = $request->scope_id;

        $existingDraft = VulnerabilitySet::where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->whereNull('published_at')
            ->first();

        if ($existingDraft) {
            return back()->with('error', 'Masih ada draft versi yang belum dipublish. Selesaikan atau hapus draft tersebut terlebih dahulu.');
        }

        $newVersi = VulnerabilitySet::nextVersion($scopeType, $scopeId);

        DB::transaction(function () use ($scopeType, $scopeId, $newVersi) {
            $newSet = VulnerabilitySet::create([
                'scope_type'        => $scopeType,
                'scope_id'          => $scopeId,
                'versi'             => $newVersi,
                'is_active'         => false,
                'catatan_perubahan' => null,
                'created_by'        => auth()->id(),
                'published_at'      => null,
            ]);

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
                        'ancaman_tipikal'  => $item->ancaman_tipikal,
                        'kategori'         => $item->kategori,
                        'dampak_tipikal'   => $item->dampak_tipikal,
                        'area_dampak'      => $item->area_dampak,
                        'kontrol_tipikal'  => $item->kontrol_tipikal,
                        'mitigasi_tipikal' => $item->mitigasi_tipikal,
                        'catatan_platform' => $item->catatan_platform,
                    ]);
                }
            }
        });

        return back()->with('success', "Draft versi {$newVersi} berhasil dibuat. Silakan edit item sebelum dipublish.");
    }

    // =========================================================================
    // PUBLISH DRAFT
    // =========================================================================
    public function publishVersion(Request $request, VulnerabilitySet $set)
    {
        $request->validate([
            'catatan_perubahan' => 'required|string|min:10',
        ]);

        abort_if($set->isPublished(), 403, 'Versi ini sudah dipublish dan tidak dapat diubah.');
        abort_if($set->items()->count() === 0, 422, 'Tidak dapat mempublish versi tanpa item kerawanan.');

        DB::transaction(function () use ($set, $request) {
            VulnerabilitySet::where('scope_type', $set->scope_type)
                ->where('scope_id', $set->scope_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

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
    // HAPUS DRAFT
    // =========================================================================
    public function deleteDraft(VulnerabilitySet $set)
    {
        abort_if($set->isPublished(), 403, 'Versi yang sudah dipublish tidak dapat dihapus.');
        $set->items()->delete();
        $set->delete();
        return back()->with('success', 'Draft versi berhasil dihapus.');
    }

    // =========================================================================
    // STORE ITEM
    // =========================================================================
    public function storeItem(Request $request, VulnerabilitySet $set)
    {
        abort_if($set->isPublished(), 403, 'Set sudah dipublish, tidak dapat menambah item.');

        $validated = $request->validate([
            'deskripsi'        => 'required|string|max:2000',
            'ancaman_tipikal'  => 'nullable|string|max:2000',
            'kategori'         => 'nullable|string|max:100',
            'dampak_tipikal'   => 'nullable|string|max:2000',
            'area_dampak'      => 'nullable|array',
            'area_dampak.*'    => 'string',
            'kontrol_tipikal'  => 'nullable|string|max:2000',
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
            'deskripsi'        => 'required|string|max:2000',
            'ancaman_tipikal'  => 'nullable|string|max:2000',
            'kategori'         => 'nullable|string|max:100',
            'dampak_tipikal'   => 'nullable|string|max:2000',
            'area_dampak'      => 'nullable|array',
            'area_dampak.*'    => 'string',
            'kontrol_tipikal'  => 'nullable|string|max:2000',
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

        // Re-number
        $set->items()->orderBy('nomor_urut')->get()->each(function ($i, $idx) {
            $i->update(['nomor_urut' => $idx + 1]);
        });

        return back()->with('success', 'Item kerawanan berhasil dihapus.');
    }

    // =========================================================================
    // REORDER ITEMS (AJAX)
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
    // LIHAT VERSI HISTORIS
    // =========================================================================
    public function showVersion(VulnerabilitySet $set)
    {
        $set->load('items', 'createdBy', 'publishedBy');

        if ($set->scope_type === 'global_class') {
            $scope = DB::table('klasifikasi_asets')->where('id', $set->scope_id)->first();
            $scopeParent = null;
        } else {
            $scope = DB::table('sub_klasifikasi_asets')->where('id', $set->scope_id)->first();
            $scopeParent = $scope
                ? DB::table('klasifikasi_asets')->where('id', $scope->klasifikasi_aset_id)->first()
                : null;
        }

        return view('admin.master-kerawanan.show-version', compact('set', 'scope', 'scopeParent'));
    }
}
