<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAktif;
use Illuminate\Http\Request;

class TahunContextController extends Controller
{
    public function setContext(Request $request)
    {
        $request->validate([
            'tahunaktif_id' => ['required', 'exists:tahunaktifs,id'],
        ]);

        session(['tahun_context' => $request->tahunaktif_id]);

        return response()->json(['ok' => true]);
    }
}
