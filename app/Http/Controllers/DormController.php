<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dorm;
use App\Models\Pelajar;

class DormController extends Controller
{
    /**
     * Senarai dorm.
     */
    public function index()
    {
        $dorms = Dorm::with('pelajars')->withCount('pelajars')->orderBy('blok')->orderBy('nama_dorm')->get();
        return view('dorm.userlist', compact('dorms'));
    }

    /**
     * Senarai dorm untuk user biasa.
     */
    public function userList()
    {
        $dorms = Dorm::with('pelajars')->withCount('pelajars')->orderBy('blok')->orderBy('nama_dorm')->get();
        return view('dorm.userlist', compact('dorms'));
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
        ]);

        // Tukar textarea senarai pelajar kepada array mudah

        Dorm::create([
            'nama_dorm' => $validated['nama_dorm'],
            'blok' => $validated['blok'],
            'capacity' => $validated['capacity'] ?? null,
        ]);

        return redirect()
            ->route('dorms.index')
            ->with('success', 'Dorm berjaya ditambah.');
    }

    /**
     * Papar butiran dorm (view members).
     */
    public function show($id)
    {
        $dorm = Dorm::with('pelajars')->findOrFail($id);
        return view('dorm.show', compact('dorm'));
    }

    /**
     * Papar borang edit dorm.
     */
    public function edit($id)
    {
        $dorm = Dorm::with('pelajars')->findOrFail($id);
        return view('dorm.edit', compact('dorm'));
    }

    /**
     * Kemaskini dorm.
     */
    public function update(Request $request, $id)
    {
        $dorm = Dorm::findOrFail($id);

        $validated = $request->validate([
            'nama_dorm' => ['required', 'string', 'max:255'],
            'blok' => ['required', 'string', 'max:10'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'senarai_pelajar' => ['nullable', 'string'], // --- IGNORE ---
        ]);

        $senarai = json_decode($validated['senarai_pelajar'] ?? '[]', true);
        // Tukar textarea senarai pelajar kepada array mudah

        $dorm->update([
            'nama_dorm' => $validated['nama_dorm'],
            'blok' => $validated['blok'],
            'capacity' => $validated['capacity'] ?? null,
            'senarai_pelajar' => $senarai, // --- IGNORE ---
        ]);

        return redirect()
            ->route('dorms.index')
            ->with('success', 'Dorm berjaya dikemaskini.');
    }


    /**
 * Simpan pelajar baru ke dorm.
 */
public function tambahPelajar(Request $request, $id)
{
    $dorm = Dorm::findOrFail($id);

    // Kira bilangan pelajar semasa
    $currentCount = Pelajar::where('dorm_id', $dorm->id_dorm)->count();

    // Kalau capacity ditetapkan & dah penuh
    if (!is_null($dorm->capacity) && $currentCount >= $dorm->capacity) {
        return response()->json([
            'success' => false,
            'message' => 'Kapasiti dorm telah penuh'
        ], 422);
    }

    // Handle JSON / form
    $data = $request->isJson() ? $request->json()->all() : $request->all();

    $validated = validator($data, [
        'nama' => 'required|string|max:255',
        'no_ic' => 'required|string|max:20|unique:pelajars,no_ic',
        'jantina' => 'required|in:L,P',
    ])->validate();

    $pelajar = Pelajar::create([
        'nama' => $validated['nama'],
        'no_ic' => $validated['no_ic'],
        'jantina' => $validated['jantina'],
        'dorm_id' => $dorm->id_dorm,
    ]);

    return response()->json([
        'success' => true,
        'pelajar' => $pelajar
    ]);
}


public function destroy($id)
{
    $dorm = Dorm::findOrFail($id);
    $dorm->delete();

    return redirect()
        ->route('dorms.index')
        ->with('success', 'Dorm berjaya dipadam.');
}

public function updatePelajar(Request $request, $idDorm, $idPelajar)
{
    $pelajar = Pelajar::where('dorm_id', $idDorm)
        ->where('id', $idPelajar)
        ->firstOrFail();

    $data = $request->isJson()
        ? $request->json()->all()
        : $request->all();

    $validated = validator($data, [
        'nama' => 'required|string|max:255',
        'no_ic' => 'required|string|max:20|unique:pelajars,no_ic,' . $pelajar->id,
        'jantina' => 'required|in:L,P',
    ])->validate();

    $pelajar->update($validated);

    return response()->json([
        'success' => true,
        'pelajar' => $pelajar
    ]);
}

public function destroyPelajar($idDorm, $idPelajar)
{
    $pelajar = Pelajar::where('dorm_id', $idDorm)
        ->where('id', $idPelajar)
        ->firstOrFail();

    $pelajar->delete();

    return response()->json([
        'success' => true,
        'message' => 'Pelajar berjaya dipadam'
    ]);
}

}

