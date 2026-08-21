<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AppSettingController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.pengaturan.tampilan', [
            'title' => 'Pengaturan Tampilan',
            'setting' => AppSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'logo_light' => ['nullable', 'image', 'mimes:svg,png,jpg,jpeg,webp', 'max:1024'],
            'logo_dark' => ['nullable', 'image', 'mimes:svg,png,jpg,jpeg,webp', 'max:1024'],
            'logo_icon' => ['nullable', 'image', 'mimes:svg,png,jpg,jpeg,webp', 'max:1024'],
        ]);

        $setting = AppSetting::current();
        $setting->app_name = $data['app_name'] ?? null;

        foreach (['logo_light', 'logo_dark', 'logo_icon'] as $field) {
            if ($request->hasFile($field)) {
                if ($setting->{$field}) {
                    Storage::disk('public')->delete($setting->{$field});
                }
                $setting->{$field} = $request->file($field)->store('branding', 'public');
            }
        }

        $setting->save();

        return back()->with('status', 'Pengaturan tampilan berhasil disimpan.');
    }

    public function reset(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'field' => ['required', 'in:logo_light,logo_dark,logo_icon'],
        ]);

        $setting = AppSetting::current();
        $field = $data['field'];

        if ($setting->{$field}) {
            Storage::disk('public')->delete($setting->{$field});
            $setting->{$field} = null;
            $setting->save();
        }

        return back()->with('status', 'Logo dikembalikan ke bawaan.');
    }
}
