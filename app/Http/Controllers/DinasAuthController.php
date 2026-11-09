<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DinasAuthController extends Controller
{
    /**
     * Tampilkan halaman login dinas (Searchable Dropdown + PIN)
     */
    public function showLogin()
    {
        if (Auth::guard('dinas')->check()) {
            $dinas = Auth::guard('dinas')->user();
            return $dinas->is_first_login
                ? redirect()->route('dinas.first_login')
                : redirect()->route('dinas.portal');
        }

        $dinasList = Dinas::orderBy('nama_dinas', 'asc')->get(['id', 'nama_dinas']);
        return view('dinas.login', compact('dinasList'));
    }

    /**
     * Proses autentikasi login dinas
     */
    public function login(Request $request)
    {
        $request->validate([
            'dinas_id' => 'required|exists:dinas,id',
            'pin' => 'required|string',
        ], [
            'dinas_id.required' => 'Silakan pilih dinas Anda terlebih dahulu.',
            'dinas_id.exists' => 'Dinas yang dipilih tidak ditemukan dalam sistem.',
            'pin.required' => 'Silakan masukkan PIN.',
        ]);

        $dinas = Dinas::findOrFail($request->dinas_id);

        if (!$dinas->verifyPin($request->pin)) {
            return back()->withInput()->with('error', 'PIN yang Anda masukkan salah. Silakan periksa kembali atau hubungi Admin BPS.');
        }

        // Login ke guard dinas
        Auth::guard('dinas')->login($dinas);

        $dinas->update([
            'last_login_at' => now(),
        ]);

        if ($dinas->is_first_login) {
            return redirect()->route('dinas.first_login')->with('info', 'Selamat datang! Karena ini pertama kali login, mohon lengkapi identitas penanggung jawab data.');
        }

        return redirect()->route('dinas.portal')->with('success', 'Selamat datang kembali, ' . $dinas->nama_dinas . '!');
    }

    /**
     * Tampilkan form pengisian PIC & opsi ganti PIN pertama kali
     */
    public function showFirstLogin()
    {
        $dinas = Auth::guard('dinas')->user();
        return view('dinas.first-login', compact('dinas'));
    }

    /**
     * Simpan data profil PIC dan opsi ganti PIN
     */
    public function saveFirstLogin(Request $request)
    {
        $request->validate([
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:30',
            'ganti_pin' => 'nullable|boolean',
            'new_pin' => 'required_if:ganti_pin,1|nullable|string|min:4|max:20|confirmed',
        ], [
            'pic_name.required' => 'Nama penanggung jawab (PIC) wajib diisi.',
            'pic_phone.required' => 'Nomor WhatsApp / Telepon PIC wajib diisi.',
            'new_pin.required_if' => 'PIN baru harus diisi jika Anda memilih untuk mengganti PIN.',
            'new_pin.min' => 'PIN baru minimal terdiri dari 4 digit/karakter.',
            'new_pin.confirmed' => 'Konfirmasi PIN baru tidak cocok.',
        ]);

        /** @var \App\Models\Dinas $dinas */
        $dinas = Auth::guard('dinas')->user();

        $updateData = [
            'pic_name' => trim($request->pic_name),
            'pic_phone' => trim($request->pic_phone),
            'is_first_login' => false,
        ];

        // Jika dinas memilih untuk ganti PIN
        if ($request->boolean('ganti_pin') && !empty($request->new_pin)) {
            $updateData['pin'] = $request->new_pin;
        }

        $dinas->update($updateData);

        return redirect()->route('dinas.portal')->with('success', 'Data penanggung jawab berhasil disimpan! Anda sekarang dapat mengakses Google Spreadsheet.');
    }

    /**
     * Logout role dinas
     */
    public function logout(Request $request)
    {
        Auth::guard('dinas')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dinas.login')->with('info', 'Anda telah berhasil keluar dari sesi.');
    }
}

