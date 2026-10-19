<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    public function index(Request $request)
    {
        $query = Camera::query();

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('merek')) {
            $query->where('merek', $request->merek);
        }

        return view('katalog.index', [
            'cameras' => $query->get(),
            'mereks'  => Camera::select('merek')->distinct()->pluck('merek'),
        ]);
    }

    public function show(Camera $camera)
    {
        return view('katalog.show', compact('camera'));
    }
}
