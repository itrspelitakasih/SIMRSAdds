<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSetting;
use App\Services\GowaService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WhatsappSettingController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.pengaturan.whatsapp', [
            'title' => 'Pengaturan WhatsApp',
            'setting' => WhatsappSetting::current(),
            'defaultTemplates' => WhatsappSetting::defaultTemplates(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'base_url' => ['nullable', 'string', 'max:255'],
            'port' => ['nullable', 'string', 'max:10'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'notify_admin_number' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'msg_ticket_created_admin' => ['nullable', 'string', 'max:2000'],
            'msg_ticket_created_reporter' => ['nullable', 'string', 'max:2000'],
            'msg_ticket_completed' => ['nullable', 'string', 'max:2000'],
            'msg_document_reminder' => ['nullable', 'string', 'max:2000'],
        ]);

        $setting = WhatsappSetting::current();

        $setting->fill([
            'base_url' => $data['base_url'] ?? null,
            'port' => $data['port'] ?? null,
            'username' => $data['username'] ?? null,
            'device_id' => $data['device_id'] ?? null,
            'notify_admin_number' => $data['notify_admin_number'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'msg_ticket_created_admin' => $data['msg_ticket_created_admin'] ?? null,
            'msg_ticket_created_reporter' => $data['msg_ticket_created_reporter'] ?? null,
            'msg_ticket_completed' => $data['msg_ticket_completed'] ?? null,
            'msg_document_reminder' => $data['msg_document_reminder'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $setting->password = $data['password'];
        }

        $setting->save();

        return back()->with('status', 'Pengaturan WhatsApp berhasil disimpan.');
    }

    public function test(Request $request, GowaService $gowa): RedirectResponse
    {
        $data = $request->validate([
            'test_phone' => ['required', 'string'],
        ]);

        $success = $gowa->send(
            $data['test_phone'],
            'Ini adalah pesan uji coba dari sistem E-Tiket IT Rumah Sakit.',
            null,
            'test'
        );

        return back()->with(
            $success ? 'status' : 'error_status',
            $success ? 'Pesan uji coba berhasil dikirim.' : 'Gagal mengirim pesan uji coba. Periksa pengaturan & log notifikasi.'
        );
    }
}
