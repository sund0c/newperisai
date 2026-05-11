<?php
// ============================================================
// FILE: app/Http/Controllers/Admin/AssetDetailController.php
// ============================================================
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetDetailPl;
use App\Models\AssetDetailPk;
use App\Models\AssetDetailSp;
use App\Models\AssetDetailSk;
use App\Models\AssetDetailDi;
use Illuminate\Http\Request;

class AssetDetailController extends Controller
{
    // ── String FQCN — tidak pakai ::class di dalam const
    private const MODEL_MAP = [
        'pl' => 'App\Models\AssetDetailPl',
        'pk' => 'App\Models\AssetDetailPk',
        'sp' => 'App\Models\AssetDetailSp',
        'sk' => 'App\Models\AssetDetailSk',
        'di' => 'App\Models\AssetDetailDi',
    ];

    // ── Resolve kodeklas dari aset (dengan eager load relasi)
    private function kodeklas(Asset $asset): string
    {
        if (! $asset->relationLoaded('subKlasifikasi')) {
            $asset->load('subKlasifikasi.klasifikasi');
        }
        return strtolower($asset->subKlasifikasi?->klasifikasi?->kodeklas ?? '');
    }

    // ── Validation rules per klasifikasi
    private function rulesFor(string $kodeklas, Request $request): array
    {
        return match ($kodeklas) {

            'pl' => [
                // url: boleh kosong, boleh '-', boleh URL valid
                'url'                  => ['required', 'url', 'max:500', function ($attr, $val, $fail) {
                    if (!preg_match('#^https?://#i', $val)) {
                        $fail('URL harus diawali dengan http:// atau https://');
                    }
                }],
                'versi'                => ['required', 'string', 'max:50'],
                'lisensi'              => ['required', 'in:Proprietary,Open Source,Freeware,In-House'],
                'tgl_lisensi_berakhir' => [
                    $request->input('lisensi') === 'Proprietary' ? 'required' : 'nullable',
                    'date',
                ],
                'vendor'               => ['required', 'in:Diskominfos Prov Bali,Mandiri,Pihak Ketiga'],
                'lead_developer'       => [
                    in_array($request->input('vendor'), ['Mandiri', 'Pihak Ketiga']) ? 'required' : 'nullable',
                    'string',
                    'max:200',
                ],
                'platform'             => ['required', 'in:Web,Mobile,Desktop'],
                'lokasi_hosting'       => ['required', 'in:Pusat Data BALIPROV,PDN KOMDIGI,Cloud AWS Diskominfos Prov Bali,Lain-lain'],
                'nama_server_lainnya'  => [
                    $request->input('lokasi_hosting') === 'Lain-lain' ? 'required' : 'nullable',
                    'string',
                    'max:200',
                ],
                'nama_server'          => ['required', 'string', 'max:200'],
            ],

            'pk' => [
                'merk'            => ['required', 'string', 'max:100'],
                'model'           => ['required', 'string', 'max:100'],
                'serial_number'   => ['required', 'string', 'max:100'],
                'tahun_perolehan' => ['required', 'integer', 'min:1990', 'max:2099'],
                'kondisi'         => ['required', 'in:Baik,Rusak Ringan,Rusak Berat'],
                'lokasi_fisik'    => ['required', 'string', 'max:300'],
                'ip_address'      => ['required', 'string', 'max:50'],
                'spesifikasi'     => ['required', 'string', 'max:2000'],
            ],

            'sp' => [
                'merk'            => ['required', 'string', 'max:100'],
                'model'           => ['required', 'string', 'max:100'],
                'serial_number'   => ['required', 'string', 'max:100'],
                'kapasitas'       => ['required', 'string', 'max:100'],
                'tahun_perolehan' => ['required', 'integer', 'min:1990', 'max:2099'],
                'kondisi'         => ['required', 'in:Baik,Rusak Ringan,Rusak Berat'],
                'lokasi_fisik'    => ['required', 'string', 'max:300'],
            ],

            'sk' => [
                'jabatan'              => ['required', 'string', 'max:200'],
                'unit_kerja'           => ['required', 'string', 'max:200'],
                'no_hp'                => ['required', 'digits_between:8,15'],
                'email'                => ['required', 'email', 'max:100'],
                'tipe'                 => ['required', 'in:Internal,Vendor,Kontraktor'],
                'akses_sistem'         => ['required', 'string'],
                'tgl_kontrak_berakhir' => ['nullable', 'date'],
            ],

            'di' => [
                'bentuk'            => ['required', 'in:Elektronik,Fisik,Keduanya'],
                'lokasi_fisik'      => [
                    in_array($request->input('bentuk'), ['Fisik', 'Keduanya'])
                        ? 'required' : 'nullable',
                    'string',
                    'max:300',
                ],
                'lokasi_elektronik' => [
                    in_array($request->input('bentuk'), ['Elektronik', 'Keduanya'])
                        ? 'required' : 'nullable',
                    'string',
                    'max:300',
                ],
                'format'           => ['required', 'in:Dokumen,Spreadsheet,Database,Laporan,Rekaman,Sertifikat,Source Code,Lainnya'],
                'klasifikasi_data' => ['required', 'in:Publik,Terbatas,Rahasia,Sangat Rahasia'],
                'retensi'          => ['required', 'string', 'max:100', function ($attr, $val, $fail) {
                    if (strtolower($val) !== 'permanen' && !is_numeric(str_replace(',', '.', $val))) {
                        $fail('Retensi harus berupa angka (cth: 5 atau 2,5) atau teks "Permanen".');
                    }
                }],
                'enkripsi'         => ['required', 'in:Ya,Tidak'],
            ],

            default => [],
        };
    }

    // ── Normalise data sebelum simpan ke DB
    private function normalise(array $data): array
    {
        // tahun_perolehan: empty string -> null
        if (array_key_exists('tahun_perolehan', $data) && $data['tahun_perolehan'] === '') {
            $data['tahun_perolehan'] = null;
        }

        // DI: hapus lokasi yang tidak relevan dengan bentuk
        if (array_key_exists('bentuk', $data)) {
            if ($data['bentuk'] === 'Elektronik') {
                $data['lokasi_fisik'] = null;
            }
            if ($data['bentuk'] === 'Fisik') {
                $data['lokasi_elektronik'] = null;
            }
        }

        // PL: hapus lead_developer jika vendor bukan Mandiri/Pihak Ketiga
        if (array_key_exists('vendor', $data)) {
            if (!in_array($data['vendor'], ['Mandiri', 'Pihak Ketiga'])) {
                $data['lead_developer'] = null;
            }
        }

        // PL: hapus nama_server_lainnya jika lokasi_hosting bukan Lain-lain
        if (array_key_exists('lokasi_hosting', $data)) {
            if ($data['lokasi_hosting'] !== 'Lain-lain') {
                $data['nama_server_lainnya'] = null;
            }
        }

        // DI: hapus metode_enkripsi jika enkripsi bukan Ya
        if (array_key_exists('enkripsi', $data)) {
            if ($data['enkripsi'] !== 'Ya') {
                $data['metode_enkripsi'] = null;
            }
        }

        return $data;
    }

    // ──────────────────────────────────────────────────────────
    // GET /admin/assets/{asset}/detail
    // ──────────────────────────────────────────────────────────
    public function show(Asset $asset)
    {
        abort_if($asset->trashed(), 404);

        $asset->load([
            'opd',
            'subKlasifikasi.klasifikasi',
            'tahunAktif',
            'detailPl',
            'detailPk',
            'detailSp',
            'detailSk',
            'detailDi',
        ]);

        $kodeklas = $this->kodeklas($asset);
        $detail   = $asset->detail();

        return view('admin.assets.detail', compact('asset', 'kodeklas', 'detail'));
    }

    // ──────────────────────────────────────────────────────────
    // POST /admin/assets/{asset}/detail
    // ──────────────────────────────────────────────────────────
    public function store(Request $request, Asset $asset)
    {
        abort_if($asset->trashed(), 404);

        $kodeklas   = $this->kodeklas($asset);
        $rules      = $this->rulesFor($kodeklas, $request);
        abort_if(empty($rules), 422, 'Klasifikasi tidak didukung.');

        $validated  = $this->normalise($request->validate($rules));
        $modelClass = self::MODEL_MAP[$kodeklas];

        $modelClass::create(array_merge($validated, ['asset_id' => $asset->id]));

        return redirect()
            ->route('admin.assets.detail', $asset)
            ->with('success', 'Detail aset berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────
    // PUT /admin/assets/{asset}/detail
    // ──────────────────────────────────────────────────────────
    public function update(Request $request, Asset $asset)
    {
        abort_if($asset->trashed(), 404);

        $kodeklas   = $this->kodeklas($asset);
        $rules      = $this->rulesFor($kodeklas, $request);
        abort_if(empty($rules), 422, 'Klasifikasi tidak didukung.');

        $validated  = $this->normalise($request->validate($rules));
        $modelClass = self::MODEL_MAP[$kodeklas];

        $detail = $modelClass::where('asset_id', $asset->id)->firstOrFail();
        $detail->update($validated);

        return redirect()
            ->route('admin.assets.detail', $asset)
            ->with('success', 'Detail aset berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────
    // GET /admin/assets/{asset}/detail/export-pdf
    // ──────────────────────────────────────────────────────────
    public function exportPdf(Asset $asset)
    {
        abort_if($asset->trashed(), 404);

        $asset->load([
            'opd',
            'subKlasifikasi.klasifikasi',
            'tahunAktif',
            'detailPl',
            'detailPk',
            'detailSp',
            'detailSk',
            'detailDi',
        ]);

        $kodeklas = $this->kodeklas($asset);
        $detail   = $asset->detail();
        abort_if(!$detail, 404, 'Data detail belum diisi.');

        // Bangun payload untuk Python script
        $basicInfo = [
            'kode_aset'       => $asset->kode_aset,
            'nama_aset'       => $asset->nama_aset,
            'keterangan'      => $asset->keterangan ?? '-',
            'opd'             => $asset->opd?->namaopd ?? '-',
            'klasifikasi'     => $asset->subKlasifikasi?->klasifikasi?->klasifikasiaset ?? '-',
            'sub_klasifikasi' => $asset->subKlasifikasi?->subklasifikasiaset ?? '-',
            'tahun'           => $asset->tahunAktif?->tahun ?? '-',
        ];

        // Detail fields — ambil semua attribute kecuali timestamps & softdelete
        // Format date fields sebagai 'Y-m-d' string agar konsisten di Python
        $dateFields = ['tgl_lisensi_berakhir', 'tgl_kontrak_berakhir'];
        $detailData = collect($detail->toArray())
            ->except(['id', 'asset_id', 'created_at', 'updated_at', 'deleted_at'])
            ->map(function ($val, $key) use ($detail, $dateFields) {
                if (in_array($key, $dateFields)) {
                    $carbon = $detail->$key; // Carbon instance via cast
                    return $carbon ? $carbon->format('Y-m-d') : null;
                }
                return $val;
            })
            ->toArray();

        $meta = [
            'pemilik_aset' => $asset->opd?->namaopd ?? 'PEMERINTAH PROVINSI BALI',
            'klasifikasi'  => $asset->subKlasifikasi?->klasifikasi?->klasifikasiaset ?? 'Semua Klasifikasi',
            'tahun'        => $asset->tahunAktif?->tahun ?? now()->year,
            'generated_at' => now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm'),
        ];

        $payload  = json_encode([
            'meta'        => $meta,
            'basic_info'  => $basicInfo,
            'detail_data' => $detailData,
            'kodeklas'    => $kodeklas,
        ]);

        $script  = base_path('scripts/generate_asset_detail_pdf.py');
        $tmpFile = sys_get_temp_dir() . '/perisai_detail_' . \Illuminate\Support\Str::random(8) . '.pdf';

        $process = new \Symfony\Component\Process\Process(
            ['python3', $script, $tmpFile],
            null,
            null,
            $payload,
            60
        );
        $process->run();

        if (! $process->isSuccessful() || ! file_exists($tmpFile)) {
            \Log::error('Detail PDF generation failed', [
                'stderr' => $process->getErrorOutput(),
            ]);
            abort(500, 'Gagal generate PDF.');
        }

        $filename = 'PERISAI_Detail_' . $asset->kode_aset . '_' . now()->format('Ymd_His') . '.pdf';

        return response()->file($tmpFile, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ])->deleteFileAfterSend(true);
    }
}
