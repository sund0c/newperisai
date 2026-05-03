<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function index(Request $request)
    {
        $sortBy    = in_array($request->sort, ['id', 'namaopd', 'created_at']) ? $request->sort : 'namaopd';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';
        $status    = $request->status;

        $query = Opd::withTrashed()
            ->when($request->search, fn($q) => $q->where('namaopd', 'like', '%' . $request->search . '%'))
            ->when($status === 'active',  fn($q) => $q->whereNull('deleted_at'))
            ->when($status === 'deleted', fn($q) => $q->whereNotNull('deleted_at'))
            ->orderBy($sortBy, $direction);

        $opds = $query->get();

        return view('admin.opd.index', compact('opds', 'sortBy', 'direction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaopd' => ['required', 'string', 'max:255'],
        ]);

        Opd::create(['namaopd' => $request->namaopd]);

        return redirect()
            ->route('admin.opd.index')
            ->with('success', 'Perangkat Daerah berhasil ditambahkan.');
    }

    public function update(Request $request, Opd $opd)
    {
        $request->validate([
            'namaopd' => ['required', 'string', 'max:255'],
        ]);

        $opd->update(['namaopd' => $request->namaopd]);

        return redirect()
            ->route('admin.opd.index')
            ->with('success', 'Perangkat Daerah berhasil diperbarui.');
    }

    public function destroy(Opd $opd)
    {
        $opd->delete();

        return redirect()
            ->route('admin.opd.index')
            ->with('success', 'Perangkat Daerah berhasil dihapus.');
    }

    public function restore($id)
    {
        Opd::withTrashed()->findOrFail($id)->restore();

        return redirect()
            ->route('admin.opd.index')
            ->with('success', 'Perangkat Daerah berhasil dipulihkan.');
    }
}
