<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.document-types.index', [
            'title' => 'Jenis Dokumen',
            'documentTypes' => DocumentType::withCount('documents')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.document-types.create', ['title' => 'Tambah Jenis Dokumen']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:document_types,name'],
        ]);

        DocumentType::create($data);

        return redirect()->route('document-types.index')->with('status', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function edit(DocumentType $documentType): View
    {
        return view('pages.admin.document-types.edit', ['title' => 'Ubah Jenis Dokumen', 'documentType' => $documentType]);
    }

    public function update(Request $request, DocumentType $documentType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:document_types,name,'.$documentType->id],
        ]);

        $documentType->update($data);

        return redirect()->route('document-types.index')->with('status', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        if ($documentType->documents()->exists()) {
            return back()->withErrors(['document_type' => 'Jenis dokumen tidak dapat dihapus karena masih digunakan.']);
        }

        $documentType->delete();

        return back()->with('status', 'Jenis dokumen berhasil dihapus.');
    }
}
