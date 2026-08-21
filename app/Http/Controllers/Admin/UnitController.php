<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.units.index', [
            'title' => 'Unit',
            'units' => Unit::withCount('tickets')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.units.create', ['title' => 'Tambah Unit']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:units,name'],
        ]);

        Unit::create($data);

        return redirect()->route('units.index')->with('status', 'Unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit): View
    {
        return view('pages.admin.units.edit', ['title' => 'Ubah Unit', 'unit' => $unit]);
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:units,name,'.$unit->id],
        ]);

        $unit->update($data);

        return redirect()->route('units.index')->with('status', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->tickets()->exists()) {
            return back()->withErrors(['unit' => 'Unit tidak dapat dihapus karena masih memiliki tiket.']);
        }

        $unit->delete();

        return back()->with('status', 'Unit berhasil dihapus.');
    }
}
