<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dorm;

class DormController extends Controller
{
    /**
     * Senarai dorm.
     */
    public function index()
    {
        $dorms = Dorm::orderBy('blok')->orderBy('nama_dorm')->get();
        return view('dorm.index', compact('dorms'));
    }

    /**
     * Papar borang tambah dorm.
     */
    public function create()
    {
        return view('dorm.create');
    }

    /**
     * Simpan dorm baharu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_dorm' => ['required', 'string', 'max:255'],
            'blok' => ['required', 'string', 'max:10'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'senarai_pelajar' => ['nullable', 'string'],
        ]);

        // Tukar textarea senarai pelajar kepada array mudah
        $senarai = collect(preg_split('/\r\n|\r|\n/', $validated['senarai_pelajar'] ?? ''))
            ->map(fn($v) => trim($v))
            ->filter()
            ->values()
            ->toArray();

        Dorm::create([
            'nama_dorm' => $validated['nama_dorm'],
            'blok' => $validated['blok'],
            'capacity' => $validated['capacity'] ?? null,
            'senarai_pelajar' => $senarai,
        ]);

        return redirect()
            ->route('dorms.index')
            ->with('success', 'Dorm berjaya ditambah.');
    }
}

