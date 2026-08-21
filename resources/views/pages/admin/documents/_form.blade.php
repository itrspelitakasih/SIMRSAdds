@php
    $document = $document ?? null;
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama / Judul Dokumen</label>
        <input type="text" name="title" value="{{ old('title', $document->title ?? '') }}" required placeholder="mis. SK Penunjukan Penanggung Jawab IT"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jenis Dokumen</label>
        <select name="document_type_id" required
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Pilih jenis dokumen</option>
            @foreach ($documentTypes as $documentType)
                <option value="{{ $documentType->id }}" @selected((string) old('document_type_id', $document->document_type_id ?? '') === (string) $documentType->id)>{{ $documentType->name }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-400">Belum ada jenis yang sesuai? Tambahkan lewat menu Master Data &raquo; <a href="{{ route('document-types.create') }}" class="text-brand-500 hover:underline">Jenis Dokumen</a>.</p>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Dokumen</label>
        <input type="text" name="document_number" value="{{ old('document_number', $document->document_number ?? '') }}"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Dokumen</label>
        <input type="date" name="issued_date" value="{{ old('issued_date', optional($document->issued_date ?? null)->format('Y-m-d')) }}" onclick="this.showPicker()"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Berakhir / Harus Diperbarui</label>
        <input type="date" name="expiry_date" value="{{ old('expiry_date', optional($document->expiry_date ?? null)->format('Y-m-d')) }}" onclick="this.showPicker()"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ingatkan pada H- (hari, pisahkan koma)</label>
        <input type="text" name="reminder_days_before" value="{{ old('reminder_days_before', $document->reminder_days_before ?? '30,14,7,3,1,0') }}" placeholder="30,14,7,3,1,0"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
        <p class="mt-1 text-xs text-gray-400">Pengingat WA dikirim pada H- hari yang dicantumkan. Setelah tanggal berakhir terlewati, pengingat akan dikirim setiap hari sampai dokumen diperbarui.</p>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Tujuan Pengingat (WhatsApp)</label>
        <input type="text" name="reminder_phone" value="{{ old('reminder_phone', $document->reminder_phone ?? '') }}" placeholder="08xxxxxxxxxx (kosongkan = pakai no. admin default)"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90" />
        <p class="mt-1 text-xs text-gray-400">Nomor WA yang akan menerima pesan pengingat untuk dokumen ini.</p>
    </div>

    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Upload Dokumen</label>
        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-1.5 file:text-sm dark:border-gray-700 dark:text-white/90" />
        @if (($document->file_path ?? null))
            <p class="mt-1 text-xs text-gray-400">Berkas saat ini: {{ $document->file_original_name }}. Unggah berkas baru untuk menggantinya.</p>
            <p class="mt-1 text-xs {{ $document->google_drive_synced_at ? 'text-success-500' : 'text-gray-400' }}">
                {{ $document->google_drive_synced_at ? 'Tersinkron ke Google Drive pada '.$document->google_drive_synced_at->format('d/m/Y H:i') : 'Belum tersinkron ke Google Drive.' }}
            </p>
        @endif
    </div>

    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Catatan</label>
        <textarea name="notes" rows="3"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:text-white/90">{{ old('notes', $document->notes ?? '') }}</textarea>
    </div>

    <div class="sm:col-span-2">
        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $document->is_active ?? true)) class="h-4 w-4 rounded border-gray-300" />
            Aktifkan pengingat untuk dokumen ini
        </label>
    </div>
</div>
