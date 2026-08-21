@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Tambah Dokumen" />

    <x-common.component-card title="Form Dokumen">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="max-w-3xl space-y-4">
            @csrf
            @include('pages.admin.documents._form')
            <div class="flex gap-3">
                <x-ui.button type="submit">Simpan</x-ui.button>
                <a href="{{ route('documents.index') }}"><x-ui.button variant="outline" type="button">Batal</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
@endsection
