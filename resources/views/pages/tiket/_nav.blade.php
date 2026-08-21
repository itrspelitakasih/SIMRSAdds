@php
    $appSetting = \App\Models\AppSetting::current();
@endphp
<div class="mx-auto flex w-full max-w-2xl items-center justify-between py-6">
    <a href="{{ route('lapor.create') }}" class="text-lg font-semibold text-gray-800 dark:text-white/90">
        {{ $appSetting->app_name ?: 'E-Tiket IT Rumah Sakit' }}
    </a>
    <nav class="flex items-center gap-4 text-sm font-medium">
        <a href="{{ route('lapor.create') }}" class="text-gray-600 hover:text-brand-500 dark:text-gray-400">Buat Tiket</a>
        <a href="{{ route('lacak.form') }}" class="text-gray-600 hover:text-brand-500 dark:text-gray-400">Lacak Tiket</a>
        <a href="{{ route('signin') }}" class="text-gray-600 hover:text-brand-500 dark:text-gray-400">Login Admin</a>
    </nav>
</div>
