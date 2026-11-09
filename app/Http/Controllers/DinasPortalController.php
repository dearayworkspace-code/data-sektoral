<?php

namespace App\Http\Controllers;

use App\Models\DinasTabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DinasPortalController extends Controller
{
    /**
     * Halaman Perantara (Landing Page Dinas)
     */
    public function index()
    {
        /** @var \App\Models\Dinas $dinas */
        $dinas = Auth::guard('dinas')->user();
        
        $tabels = $dinas->tabels()->orderBy('id', 'asc')->get();

        $stats = [
            'total_tabel' => $tabels->count(),
            'data_masuk' => $tabels->where('data_masuk', true)->count(),
            'sudah_diisi' => $tabels->where('status_pengisian', 'sudah_diisi')->count(),
        ];

        return view('dinas.dashboard', compact('dinas', 'tabels', 'stats'));
    }

    /**
     * Halaman Tampilan Embed Google Spreadsheet
     */
    public function openSpreadsheet()
    {
        /** @var \App\Models\Dinas $dinas */
        $dinas = Auth::guard('dinas')->user();

        if (empty($dinas->spreadsheet_url)) {
            return redirect()->route('dinas.portal')->with('warning', 'Tautan Google Spreadsheet untuk instansi Anda belum dimappingkan oleh Admin BPS. Silakan hubungi admin untuk konfirmasi.');
        }

        $embedUrl = $dinas->embed_spreadsheet_url;
        return view('dinas.embed', compact('dinas', 'embedUrl'));
    }

    /**
     * Update profil PIC & PIN mandiri dari halaman portal
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:30',
            'current_pin' => 'nullable|string',
            'new_pin' => 'nullable|string|min:4|max:20|confirmed',
        ], [
            'pic_name.required' => 'Nama PIC wajib diisi.',
            'pic_phone.required' => 'Nomor WhatsApp PIC wajib diisi.',
            'new_pin.min' => 'PIN baru minimal 4 digit.',
            'new_pin.confirmed' => 'Konfirmasi PIN baru tidak sesuai.',
        ]);

        /** @var \App\Models\Dinas $dinas */
        $dinas = Auth::guard('dinas')->user();

        $updateData = [
            'pic_name' => trim($request->pic_name),
            'pic_phone' => trim($request->pic_phone),
        ];

        if (!empty($request->new_pin)) {
            if (!$request->filled('current_pin') || !$dinas->verifyPin($request->current_pin)) {
                return back()->with('error', 'PIN lama Anda tidak cocok. Gagal mengubah PIN.');
            }
            $updateData['pin'] = $request->new_pin;
        }

        $dinas->update($updateData);

        return back()->with('success', 'Profil dan informasi penanggung jawab berhasil diperbarui.');
    }

    /**
     * Interaktif dinas: Tandai tabel selesai diisi / batalkan tanda selesai
     */
    public function toggleTabelStatus(Request $request, $id)
    {
        /** @var \App\Models\Dinas $dinas */
        $dinas = Auth::guard('dinas')->user();

        $tabel = $dinas->tabels()->findOrFail($id);

        $newStatus = ($tabel->status_pengisian === 'sudah_diisi') ? 'belum_diisi' : 'sudah_diisi';
        $tabel->update(['status_pengisian' => $newStatus]);

        $pesan = ($newStatus === 'sudah_diisi')
            ? "Tabel '{$tabel->nama_tabel}' berhasil ditandai selesai diisi."
            : "Tanda selesai pada tabel '{$tabel->nama_tabel}' telah dibatalkan.";

        return back()->with('success', $pesan);
    }

    /**
     * Interaktif dinas: Simpan catatan atau keterangan konfirmasi untuk tabel tertentu
     */
    public function updateCatatanDinas(Request $request, $id)
    {
        /** @var \App\Models\Dinas $dinas */
        $dinas = Auth::guard('dinas')->user();

        $tabel = $dinas->tabels()->findOrFail($id);

        $tabel->update([
            'catatan_dinas' => $request->filled('catatan_dinas') ? trim($request->catatan_dinas) : null,
        ]);

        return back()->with('success', "Catatan untuk tabel '{$tabel->nama_tabel}' berhasil disimpan.");
    }
}
