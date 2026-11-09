<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use App\Models\DinasTabel;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminDashboardController extends Controller
{
    /**
     * Tampilan utama dashboard admin BPS
     */
    public function index(Request $request)
    {
        $query = Dinas::with('tabels');

        // Pencarian berdasarkan kata kunci
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_dinas', 'like', "%{$keyword}%")
                  ->orWhere('shortlink', 'like', "%{$keyword}%")
                  ->orWhere('petugas_bps', 'like', "%{$keyword}%")
                  ->orWhere('pic_name', 'like', "%{$keyword}%")
                  ->orWhere('pic_phone', 'like', "%{$keyword}%");
            });
        }

        // Filter status dinas
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('is_first_login', false);
            } elseif ($request->status === 'belum') {
                $query->where('is_first_login', true);
            } elseif ($request->status === 'tanpa_link') {
                $query->where(function ($q) {
                    $q->whereNull('spreadsheet_url')->orWhere('spreadsheet_url', '');
                });
            } elseif ($request->status === 'ada_evaluasi') {
                $query->whereNotNull('evaluasi')->where('evaluasi', '!=', '');
            }
        }

        $dinasList = $query->orderBy('nama_dinas', 'asc')->paginate(15)->withQueryString();

        // Statistik ringkas untuk kartu dashboard
        $stats = [
            'total' => Dinas::count(),
            'sudah_isi' => Dinas::where('is_first_login', false)->count(),
            'belum_login' => Dinas::where('is_first_login', true)->count(),
            'link_terpasang' => Dinas::whereNotNull('spreadsheet_url')->where('spreadsheet_url', '!=', '')->count(),
            'total_tabel' => DinasTabel::count(),
            'tabel_data_masuk' => DinasTabel::where('data_masuk', true)->count(),
        ];

        return view('admin.dashboard', compact('dinasList', 'stats'));
    }

    /**
     * Tambah dinas baru secara manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_dinas' => 'required|string|max:255|unique:dinas,nama_dinas',
            'spreadsheet_url' => 'nullable|url|max:1000',
            'shortlink' => 'nullable|string|max:255',
            'petugas_bps' => 'nullable|string|max:255',
            'evaluasi' => 'nullable|string|max:1000',
            'pin' => 'nullable|string|min:4|max:20',
        ], [
            'nama_dinas.required' => 'Nama dinas wajib diisi.',
            'nama_dinas.unique' => 'Nama dinas ini sudah terdaftar sebelumnya.',
            'spreadsheet_url.url' => 'Format URL Spreadsheet tidak valid. Contoh: https://docs.google.com/spreadsheets/d/...',
            'pin.min' => 'PIN minimal 4 karakter/digit.',
        ]);

        $pin = $request->filled('pin') ? trim($request->pin) : '123456';

        Dinas::create([
            'nama_dinas' => trim($request->nama_dinas),
            'spreadsheet_url' => $request->filled('spreadsheet_url') ? trim($request->spreadsheet_url) : null,
            'shortlink' => $request->filled('shortlink') ? trim($request->shortlink) : null,
            'petugas_bps' => $request->filled('petugas_bps') ? trim($request->petugas_bps) : null,
            'evaluasi' => $request->filled('evaluasi') ? trim($request->evaluasi) : null,
            'pin' => $pin,
            'is_first_login' => true,
        ]);

        return back()->with('success', "Dinas '{$request->nama_dinas}' berhasil ditambahkan dengan PIN default: {$pin}");
    }

    /**
     * Perbarui data dinas secara manual
     */
    public function update(Request $request, $id)
    {
        $dinas = Dinas::findOrFail($id);

        $request->validate([
            'nama_dinas' => 'required|string|max:255|unique:dinas,nama_dinas,' . $dinas->id,
            'spreadsheet_url' => 'nullable|url|max:1000',
            'shortlink' => 'nullable|string|max:255',
            'petugas_bps' => 'nullable|string|max:255',
            'evaluasi' => 'nullable|string|max:1000',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:30',
        ]);

        $dinas->update([
            'nama_dinas' => trim($request->nama_dinas),
            'spreadsheet_url' => $request->filled('spreadsheet_url') ? trim($request->spreadsheet_url) : null,
            'shortlink' => $request->filled('shortlink') ? trim($request->shortlink) : null,
            'petugas_bps' => $request->filled('petugas_bps') ? trim($request->petugas_bps) : null,
            'evaluasi' => $request->filled('evaluasi') ? trim($request->evaluasi) : null,
            'pic_name' => $request->filled('pic_name') ? trim($request->pic_name) : null,
            'pic_phone' => $request->filled('pic_phone') ? trim($request->pic_phone) : null,
        ]);

        return back()->with('success', "Data dinas '{$dinas->nama_dinas}' berhasil diperbarui.");
    }

    /**
     * Reset PIN dinas oleh Admin BPS
     */
    public function resetPin(Request $request, $id)
    {
        $dinas = Dinas::findOrFail($id);

        $request->validate([
            'pin' => 'required|string|min:4|max:20',
        ], [
            'pin.required' => 'PIN baru wajib diisi.',
            'pin.min' => 'PIN minimal 4 digit.',
        ]);

        $dinas->update([
            'pin' => trim($request->pin),
        ]);

        return back()->with('success', "PIN untuk {$dinas->nama_dinas} berhasil direset menjadi: {$request->pin}");
    }

    /**
     * Hapus dinas
     */
    public function destroy($id)
    {
        $dinas = Dinas::findOrFail($id);
        $nama = $dinas->nama_dinas;
        $dinas->delete();

        return back()->with('success', "Dinas '{$nama}' berhasil dihapus.");
    }

    /**
     * Toggle status verifikasi BPS Data Masuk (True / False) untuk tabel tertentu
     */
    public function toggleDataMasuk($id)
    {
        $tabel = DinasTabel::findOrFail($id);
        $tabel->update(['data_masuk' => !$tabel->data_masuk]);

        $statusText = $tabel->data_masuk ? 'Diterima' : 'Belum Diterima';
        return back()->with('success', "Status Data Masuk untuk tabel '{$tabel->nama_tabel}' diubah menjadi: {$statusText}.");
    }

    /**
     * Unduh template resmi import massal (Format Sheet LSDA2026)
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_LSDA2026.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Menulis BOM UTF-8 agar karakter kompatibel dibuka di Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom resmi sesuai sheet LSDA2026
            fputcsv($handle, [
                'NO',
                'WORD',
                'EVALUASI',
                'NAMA DINAS',
                'TAUTAN FILE',
                'NO TABEL',
                'NOMOR TABEL LSDA2025',
                'NAMA TABEL',
                'PETUGAS',
                'CONTACT PERSON 2024',
                'CONTACT PERSON 2025',
                'CONTACT PERSON 2026',
                'DATA MASUK',
                'SUBJEK DI WEB',
                'JUMLAH TABEL DI WEBSITE',
                'CATATAN'
            ]);

            // Baris Contoh Dinas 1 (dengan 2 tabel anak)
            fputcsv($handle, [
                '1',
                's.bps.go.id/LSDA_Disperindag',
                'Tidak ada data jumlah pedagang',
                'Dinas Perdagangan Dan Perindustrian Kabupaten Lampung Selatan',
                'https://docs.google.com/spreadsheets/d/1exampleID1/edit',
                '11.1',
                '11.1',
                'Jumlah Sarana Perdagangan Menurut Jenisnya di Kabupaten Lampung Selatan, 2022-2025',
                'INDRI',
                'SUTRISNO 081369961488',
                '',
                "LIA\n(082186404551)",
                'False',
                'INDUSTRI',
                '',
                'TIDAK ADA'
            ]);

            fputcsv($handle, [
                '',
                '',
                '',
                '',
                '',
                '11.2',
                '11.2',
                'Jumlah Bangunan Sarana Perdagangan Menurut Lokasi Pasar dan Jenis Bangunan, 2025',
                'INDRI',
                '',
                '',
                '',
                'True',
                'INDUSTRI',
                '1',
                'Sudah update 2025'
            ]);

            // Baris Contoh Dinas 2
            fputcsv($handle, [
                '2',
                's.bps.go.id/LSDA_Dinkes',
                '',
                'Dinas Kesehatan Kabupaten Lampung Selatan',
                'https://docs.google.com/spreadsheets/d/1exampleID2/edit',
                '4.2.2',
                '4.2.2',
                'Jumlah Tenaga Kesehatan Menurut Kecamatan di Kabupaten Lampung Selatan, 2025',
                'DIZA',
                '',
                '',
                "META\n(083160325446)",
                'True',
                'KESEHATAN',
                '',
                ''
            ]);

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Import massal daftar dinas & rincian tabel (Kompatibel penuh dengan sheet LSDA2026)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:15360',
        ], [
            'file.required' => 'Silakan pilih file CSV atau Excel yang akan diimpor.',
            'file.mimes' => 'Format file harus berupa CSV (.csv) atau Excel (.xlsx / .xls).',
            'file.max' => 'Ukuran file maksimal adalah 15 MB.',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $sheetRows = [];

        try {
            if ($extension === 'csv' || $extension === 'txt') {
                $sheetRows = $this->parseCsvRows($file->getRealPath());
            } else {
                $sheetRows = $this->parseExcelRows($file->getRealPath());
            }
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal membaca berkas: ' . $e->getMessage());
        }

        if (empty($sheetRows)) {
            return back()->with('error', 'Berkas kosong atau tidak ditemukan data yang valid.');
        }

        // Jalankan parser cerdas dengan mapping kolom dinamis
        $result = $this->processImportRows($sheetRows);

        if ($result['dinas_count'] === 0) {
            return back()->with('error', 'Tidak ditemukan data nama dinas yang valid dalam berkas.');
        }

        return back()->with('success', "Proses import berhasil: {$result['dinas_created']} dinas baru ditambahkan, {$result['dinas_updated']} dinas diperbarui, dan {$result['tables_imported']} rincian tabel data berhasil diproses.");
    }

    /**
     * Ekstraksi nama dan no telepon/WA dari teks kontak
     */
    private function extractContact(?string $raw): array
    {
        if (empty($raw)) {
            return ['', ''];
        }

        $text = trim($raw);
        if (preg_match('/[\(\[]?([0-9\-\+\s]{9,16})[\)\]]?/', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $phone = preg_replace('/[^0-9\+]/', '', $matches[1][0]);
            $offset = $matches[0][1];
            $matchLen = strlen($matches[0][0]);
            $name = trim(substr($text, 0, $offset) . ' ' . substr($text, $offset + $matchLen));
            $name = preg_replace('/[\(\[]?[0-9\-\+\s]{9,16}[\)\]]?/', '', $name);
            $name = trim(preg_replace('/\s+/', ' ', $name), " \n\r\t-:,()[]");
            return [$name, $phone];
        }

        return [$text, ''];
    }

    /**
     * Proses baris data yang diekstrak dari CSV atau Excel
     */
    private function processImportRows(array $allRows): array
    {
        $headerRowIdx = -1;
        $colMap = [];

        // 1. Cari baris header
        foreach ($allRows as $idx => $row) {
            $normalizedRow = array_map(function ($val) {
                return strtoupper(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$val)));
            }, $row);

            foreach ($normalizedRow as $cIdx => $cellText) {
                if (str_contains($cellText, 'NAMA DINAS')) {
                    $headerRowIdx = $idx;
                    break 2;
                }
            }
        }

        // Jika tidak ditemukan header NAMA DINAS, cek apakah format 2 kolom sederhana (Nama Dinas & Link)
        if ($headerRowIdx === -1) {
            return $this->processLegacyTwoColumnFormat($allRows);
        }

        // Petakan indeks kolom berdasarkan header
        $headers = array_map(function ($val) {
            return strtoupper(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$val)));
        }, $allRows[$headerRowIdx]);

        foreach ($headers as $colIdx => $hText) {
            if (str_contains($hText, 'NAMA DINAS')) $colMap['nama_dinas'] = $colIdx;
            elseif (str_contains($hText, 'TAUTAN FILE') || str_contains($hText, 'SPREADSHEET')) $colMap['tautan_file'] = $colIdx;
            elseif ($hText === 'WORD' || str_contains($hText, 'SHORTLINK')) $colMap['shortlink'] = $colIdx;
            elseif ($hText === 'PETUGAS') $colMap['petugas_bps'] = $colIdx;
            elseif (str_contains($hText, 'EVALUASI')) $colMap['evaluasi'] = $colIdx;
            elseif (str_contains($hText, 'CONTACT PERSON') && str_contains($hText, '2026')) $colMap['cp_2026'] = $colIdx;
            elseif (str_contains($hText, 'CONTACT PERSON') && !isset($colMap['cp_other'])) $colMap['cp_other'] = $colIdx;
            elseif ($hText === 'NO TABEL') $colMap['no_tabel'] = $colIdx;
            elseif (str_contains($hText, 'LSDA2025') || str_contains($hText, 'LSDA 2025')) $colMap['no_tabel_2025'] = $colIdx;
            elseif (str_contains($hText, 'NAMA TABEL')) $colMap['nama_tabel'] = $colIdx;
            elseif (str_contains($hText, 'DATA MASUK')) $colMap['data_masuk'] = $colIdx;
            elseif (str_contains($hText, 'SUBJEK')) $colMap['subjek_web'] = $colIdx;
            elseif (str_contains($hText, 'CATATAN')) $colMap['catatan_bps'] = $colIdx;
            elseif (str_contains($hText, 'JUMLAH TABEL')) $colMap['jumlah_tabel_web'] = $colIdx;
        }

        $dinasCreated = 0;
        $dinasUpdated = 0;
        $tablesImported = 0;
        $currentDinas = null;

        for ($i = $headerRowIdx + 1; $i < count($allRows); $i++) {
            $row = $allRows[$i];

            $namaDinas = isset($colMap['nama_dinas']) ? trim((string)($row[$colMap['nama_dinas']] ?? '')) : '';
            $tautanRaw = isset($colMap['tautan_file']) ? trim((string)($row[$colMap['tautan_file']] ?? '')) : '';
            $namaTabel = isset($colMap['nama_tabel']) ? trim((string)($row[$colMap['nama_tabel']] ?? '')) : '';

            // Abaikan baris rekapitulasi/total di bagian bawah sheet
            $noTabel2025 = isset($colMap['no_tabel_2025']) ? strtolower(trim((string)($row[$colMap['no_tabel_2025']] ?? ''))) : '';
            if (in_array($noTabel2025, ['semua', 'merah', 'total']) || strtolower($namaTabel) === 'total' || is_numeric($namaTabel)) {
                continue;
            }

            // Jika baris ini memiliki Nama Dinas baru
            if (!empty($namaDinas)) {
                $shortlink = isset($colMap['shortlink']) ? trim((string)($row[$colMap['shortlink']] ?? '')) : null;
                $petugasBps = isset($colMap['petugas_bps']) ? trim((string)($row[$colMap['petugas_bps']] ?? '')) : null;
                $evaluasi = isset($colMap['evaluasi']) ? trim((string)($row[$colMap['evaluasi']] ?? '')) : null;

                // Ambil kontak dari CP 2026 atau CP lainnya
                $cpRaw = isset($colMap['cp_2026']) ? trim((string)($row[$colMap['cp_2026']] ?? '')) : '';
                if (empty($cpRaw) && isset($colMap['cp_other'])) {
                    $cpRaw = trim((string)($row[$colMap['cp_other']] ?? ''));
                }
                [$picName, $picPhone] = $this->extractContact($cpRaw);

                $spreadsheetUrl = (str_starts_with($tautanRaw, 'http://') || str_starts_with($tautanRaw, 'https://'))
                    ? $tautanRaw
                    : null;

                $existing = Dinas::where('nama_dinas', $namaDinas)->first();

                if ($existing) {
                    $updatePayload = [
                        'shortlink' => !empty($shortlink) ? $shortlink : $existing->shortlink,
                        'petugas_bps' => !empty($petugasBps) ? $petugasBps : $existing->petugas_bps,
                        'evaluasi' => !empty($evaluasi) ? $evaluasi : $existing->evaluasi,
                    ];
                    if ($spreadsheetUrl) {
                        $updatePayload['spreadsheet_url'] = $spreadsheetUrl;
                    }
                    if (!empty($picName) && empty($existing->pic_name)) {
                        $updatePayload['pic_name'] = $picName;
                        $updatePayload['pic_phone'] = $picPhone;
                        $updatePayload['is_first_login'] = false;
                    }
                    $existing->update($updatePayload);
                    $currentDinas = $existing;
                    $dinasUpdated++;
                } else {
                    $currentDinas = Dinas::create([
                        'nama_dinas' => $namaDinas,
                        'spreadsheet_url' => $spreadsheetUrl,
                        'shortlink' => !empty($shortlink) ? $shortlink : null,
                        'petugas_bps' => !empty($petugasBps) ? $petugasBps : null,
                        'evaluasi' => !empty($evaluasi) ? $evaluasi : null,
                        'pic_name' => !empty($picName) ? $picName : null,
                        'pic_phone' => !empty($picPhone) ? $picPhone : null,
                        'pin' => '123456',
                        'is_first_login' => empty($picName),
                    ]);
                    $dinasCreated++;
                }
            }

            // Jika baris ini memuat rincian tabel untuk dinas saat ini
            if ($currentDinas && !empty($namaTabel)) {
                $noTabel = isset($colMap['no_tabel']) ? trim((string)($row[$colMap['no_tabel']] ?? '')) : null;
                $noTabel2025Raw = isset($colMap['no_tabel_2025']) ? trim((string)($row[$colMap['no_tabel_2025']] ?? '')) : null;
                $subjekWeb = isset($colMap['subjek_web']) ? trim((string)($row[$colMap['subjek_web']] ?? '')) : null;
                $catatanBps = isset($colMap['catatan_bps']) ? trim((string)($row[$colMap['catatan_bps']] ?? '')) : null;
                $jumlahTabel = isset($colMap['jumlah_tabel_web']) ? trim((string)($row[$colMap['jumlah_tabel_web']] ?? '')) : null;

                $dmRaw = isset($colMap['data_masuk']) ? trim((string)($row[$colMap['data_masuk']] ?? '')) : '';
                $dataMasuk = ($dmRaw === '1' || strtolower($dmRaw) === 'true');

                DinasTabel::updateOrCreate(
                    [
                        'dinas_id' => $currentDinas->id,
                        'nama_tabel' => $namaTabel,
                    ],
                    [
                        'no_tabel' => !empty($noTabel) ? $noTabel : null,
                        'no_tabel_lsda2025' => !empty($noTabel2025Raw) ? $noTabel2025Raw : null,
                        'subjek_web' => !empty($subjekWeb) ? $subjekWeb : null,
                        'data_masuk' => $dataMasuk,
                        'catatan_bps' => !empty($catatanBps) ? $catatanBps : null,
                        'jumlah_tabel_web' => !empty($jumlahTabel) ? $jumlahTabel : null,
                    ]
                );
                $tablesImported++;
            }
        }

        return [
            'dinas_count' => $dinasCreated + $dinasUpdated,
            'dinas_created' => $dinasCreated,
            'dinas_updated' => $dinasUpdated,
            'tables_imported' => $tablesImported,
        ];
    }

    /**
     * Fallback untuk format CSV 2 kolom warisan (Nama Dinas & Link)
     */
    private function processLegacyTwoColumnFormat(array $allRows): array
    {
        $dinasCreated = 0;
        $dinasUpdated = 0;
        $isFirst = true;

        foreach ($allRows as $row) {
            if ($isFirst) {
                $isFirst = false;
                continue;
            }

            $namaDinas = trim((string)($row[0] ?? ''));
            $link = trim((string)($row[1] ?? ''));

            if (empty($namaDinas)) {
                continue;
            }

            $dinas = Dinas::where('nama_dinas', $namaDinas)->first();
            if ($dinas) {
                $dinas->update([
                    'spreadsheet_url' => !empty($link) ? $link : $dinas->spreadsheet_url,
                ]);
                $dinasUpdated++;
            } else {
                Dinas::create([
                    'nama_dinas' => $namaDinas,
                    'spreadsheet_url' => !empty($link) ? $link : null,
                    'pin' => '123456',
                    'is_first_login' => true,
                ]);
                $dinasCreated++;
            }
        }

        return [
            'dinas_count' => $dinasCreated + $dinasUpdated,
            'dinas_created' => $dinasCreated,
            'dinas_updated' => $dinasUpdated,
            'tables_imported' => 0,
        ];
    }

    /**
     * Parse seluruh baris CSV
     */
    private function parseCsvRows(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);
            rewind($handle);
            $delimiter = (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) ? ';' : ',';

            while (($data = fgetcsv($handle, 4000, $delimiter)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Parse seluruh baris Excel (.xlsx / .xls) menggunakan PhpSpreadsheet
     * Otomatis mengutamakan sheet 'LSDA2026' jika tersedia
     */
    private function parseExcelRows(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getSheetByName('LSDA2026') ?? $spreadsheet->getActiveSheet();
        return $worksheet->toArray();
    }

    /**
     * Ekspor seluruh data dinas dan rekap PIC ke CSV
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekap_dinas_bps_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Header kolom
            fputcsv($handle, [
                'No',
                'Nama Dinas',
                'Shortlink (WORD)',
                'Petugas Pembina BPS',
                'Catatan Evaluasi',
                'Status Pengisian Akun',
                'Nama PIC',
                'Nomor Telepon/WA',
                'PIN Saat Ini',
                'Link Spreadsheet',
                'Total Tabel',
                'Tabel Selesai Diisi Dinas',
                'Tabel Diterima BPS (Data Masuk)',
                'Login Terakhir',
            ]);

            $dinasList = Dinas::with('tabels')->orderBy('nama_dinas', 'asc')->get();
            $no = 1;

            foreach ($dinasList as $dinas) {
                $totalTables = $dinas->tabels->count();
                $diisiCount = $dinas->tabels->where('status_pengisian', 'sudah_diisi')->count();
                $masukCount = $dinas->tabels->where('data_masuk', true)->count();

                fputcsv($handle, [
                    $no++,
                    $dinas->nama_dinas,
                    $dinas->shortlink ?? '-',
                    $dinas->petugas_bps ?? '-',
                    $dinas->evaluasi ?? '-',
                    $dinas->is_first_login ? 'Belum Login / Belum Lengkapi PIC' : 'Aktif / PIC Lengkap',
                    $dinas->pic_name ?? '-',
                    $dinas->pic_phone ?? '-',
                    $dinas->plain_pin,
                    $dinas->spreadsheet_url ?? '-',
                    $totalTables,
                    $diisiCount,
                    $masukCount,
                    $dinas->last_login_at ? $dinas->last_login_at->format('d/m/Y H:i') : 'Belum Pernah',
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
