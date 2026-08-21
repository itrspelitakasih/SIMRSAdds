<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleDriveSetting;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoogleDriveSettingController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.pengaturan.google-drive', [
            'title' => 'Pengaturan Google Drive',
            'setting' => GoogleDriveSetting::current(),
            'redirectUri' => route('pengaturan.google-drive.callback'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'string', 'max:255'],
            'client_secret' => ['nullable', 'string', 'max:255'],
            'folder_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $setting = GoogleDriveSetting::current();

        $setting->fill([
            'client_id' => $data['client_id'] ?? null,
            'folder_id' => $data['folder_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($data['client_secret'])) {
            $setting->client_secret = $data['client_secret'];
        }

        $setting->save();

        return back()->with('status', 'Pengaturan Google Drive berhasil disimpan.');
    }

    public function connect(GoogleDriveService $googleDrive): RedirectResponse
    {
        $setting = GoogleDriveSetting::current();

        abort_unless($setting->client_id && $setting->client_secret, 400, 'Isi dan simpan Client ID & Client Secret terlebih dahulu.');

        return redirect()->away($googleDrive->authorizationUrl(route('pengaturan.google-drive.callback')));
    }

    public function callback(Request $request, GoogleDriveService $googleDrive): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('pengaturan.google-drive')
                ->with('error_status', 'Otorisasi Google dibatalkan atau gagal: '.$request->string('error'));
        }

        $request->validate(['code' => ['required', 'string']]);

        try {
            $googleDrive->handleCallback($request->string('code')->toString(), route('pengaturan.google-drive.callback'));
        } catch (\Throwable $e) {
            return redirect()->route('pengaturan.google-drive')->with('error_status', $e->getMessage());
        }

        return redirect()->route('pengaturan.google-drive')->with('status', 'Akun Google berhasil dihubungkan.');
    }

    public function disconnect(GoogleDriveService $googleDrive): RedirectResponse
    {
        $googleDrive->disconnect();

        return back()->with('status', 'Koneksi Google Drive diputuskan.');
    }

    public function test(GoogleDriveService $googleDrive): RedirectResponse
    {
        $result = $googleDrive->testConnection();

        return back()->with($result['success'] ? 'status' : 'error_status', $result['message']);
    }
}
