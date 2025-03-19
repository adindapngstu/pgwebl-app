<?php

namespace App\Http\Controllers;

use App\Models\PolylinesModel;
use Illuminate\Http\Request;

class PolylinesController extends Controller
{
    public function __construct()
    {
        $this->polylines = new PolylinesModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Map',
        ];
        return view('map', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate(
            [
                'name' => 'required|unique:polylines,name',
                'geom_polyline' => 'required',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exists',
                'description.required' => 'Description is required',
                'geom_polyline.required' => 'Geometry polyline is required',
            ]
        );

        // Cek apakah description kosong
        if (empty($request->description)) {
            return redirect()->route('map')->with('error', 'Description is required');
        }

        $data = [
            'geom' => $request->geom_polyline,
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Buat data
        if (!$this->polylines->create($data)) {
            return redirect()->route('map')->with('error', 'Line failed to add');
        }

        return redirect()->route('map')->with('success', 'Line has been added');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
