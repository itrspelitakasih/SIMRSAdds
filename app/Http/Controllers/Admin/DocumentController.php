<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\WhatsappSetting;
use App\Services\GoogleDriveService;
use App\Services\GowaService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.documents.index', [
            'title' => 'Reminder Dokumen',
            'documents' => Document::with('documentType')->orderBy('expiry_date')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.documents.create', [
            'title' => 'Tambah Dokumen',
            'documentTypes' => DocumentType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, GoogleDriveService $googleDrive): RedirectResponse
    {
        $data = $this->validateData($request);

        $document = Document::create($this->prepareData($request, $data));

        if ($request->hasFile('file')) {
            $googleDrive->uploadDocument($document);
        }

        return redirect()->route('documents.index')->with('status', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(Document $document): View
    {
        return view('pages.admin.documents.edit', [
            'title' => 'Ubah Dokumen',
            'document' => $document,
            'documentTypes' => DocumentType::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Document $document, GoogleDriveService $googleDrive): RedirectResponse
    {
        $data = $this->validateData($request);

        $document->update($this->prepareData($request, $data, $document));

        if ($request->hasFile('file')) {
            $googleDrive->uploadDocument($document);
        }

        return redirect()->route('documents.index')->with('status', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('status', 'Dokumen berhasil dihapus.');
    }

    public function download(Document $document): StreamedResponse
    {
        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->file_original_name ?? basename($document->file_path));
    }

    public function view(Document $document): StreamedResponse
    {
        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->response($document->file_path, $document->file_original_name ?? basename($document->file_path));
    }

    public function testReminder(Document $document, GowaService $gowa): RedirectResponse
    {
        $phone = $document->reminder_phone ?: WhatsappSetting::current()->notify_admin_number;

        if (! $phone) {
            return back()->withErrors(['reminder' => 'Nomor WA pengingat belum diatur untuk dokumen ini maupun di pengaturan WhatsApp.']);
        }

        $message = "🧪 *Uji Coba Pengingat Dokumen*\n\n".
            "*Dokumen:* {$document->title}\n".
            '*Jenis:* '.($document->documentType->name ?? '-')."\n".
            '*No. Dokumen:* '.($document->document_number ?: '-')."\n".
            '*Tanggal Berakhir:* '.($document->expiry_date?->translatedFormat('d F Y') ?? '-');

        $success = $gowa->send($phone, $message, null, 'document_reminder_test');

        return back()->with(
            $success ? 'status' : 'error_status',
            $success ? 'Pesan uji coba berhasil dikirim.' : 'Gagal mengirim pesan uji coba. Periksa pengaturan WhatsApp.'
        );
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type_id' => ['required', 'exists:document_types,id'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'issued_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'reminder_days_before' => ['nullable', 'string', 'max:255', 'regex:/^\d+(,\s*\d+)*$/'],
            'reminder_phone' => ['nullable', 'string', 'max:20'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function prepareData(Request $request, array $data, ?Document $document = null): array
    {
        $data['reminder_days_before'] = $data['reminder_days_before'] ?: '30,14,7,3,1,0';
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('file')) {
            if ($document?->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $data['file_path'] = $request->file('file')->store('documents', 'public');
            $data['file_original_name'] = $request->file('file')->getClientOriginalName();
        }

        unset($data['file']);

        return $data;
    }
}
