<?php

namespace App\Http\Controllers;

use App\Models\PointsModel;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function __construct()
    {
        $this->points = new PointsModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Map',
        ];

        return view('map', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input (kembalikan validasi 'description' agar tetap wajib diisi)
        $request->validate(
            [
                'name' => 'required|unique:points,name',
                'description' => 'required', // Description kembali menjadi wajib
                'geom_point' => 'required',
            ],
            [
                'name.required' => 'Point name is required',
                'name.unique' => 'Point name already exists',
                'description.required' => 'Description is required',
                'geom_point.required' => 'Geometry point is required',
            ]
        );

        // Cek apakah description kosong
        if (empty($request->description)) {
            return redirect()->route('map')->with('error', 'Description is required');
        }

        $data = [
            'geom' => $request->geom_point,
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Buat data
        if (!$this->points->create($data)) {
            return redirect()->route('map')->with('error', 'Point failed to add');
        }

        //Redirect ke halaman peta
        return redirect()->route('map')->with('success', 'Point has been added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
