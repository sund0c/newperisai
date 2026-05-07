<?php

namespace App\Http\Middleware;

use App\Models\TahunAktif;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveTahunContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $tahunId = session('tahun_context');

        // Coba resolve dari session dulu
        $tahun = $tahunId ? TahunAktif::find($tahunId) : null;

        // Fallback ke tahun aktif jika session kosong / tahun sudah dihapus
        if (! $tahun) {
            $tahun = TahunAktif::getActive();
            session()->forget('tahun_context');
        }

        // Inject ke request object agar controller bisa akses via $request->tahunContext
        //        $request->attributes->set('tahunContext', $tahun);
        $request->merge(['tahunContext' => $tahun]);


        // Share ke semua view Blade
        View::share('tahunContext', $tahun);
        View::share('allTahun', TahunAktif::orderBy('tahun', 'desc')->get());

        return $next($request);
    }
}
