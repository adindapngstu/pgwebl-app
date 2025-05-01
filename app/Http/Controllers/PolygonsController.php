<?php

namespace App\Http\Controllers;

use App\Models\PolygonsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PolygonsController extends Controller
{
    public function __construct()
    {
        $this->polygons = new PolygonsModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Map',
        ];
        return view('map', $data);
    }

    public function store(Request $request)
{
    // Validasi data
    $request->validate(
        [
            'name' => 'required|unique:polygons,name',
            'geom_polygon' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2000',
        ],
        [
            'name.required' => 'Name is required',
            'name.unique' => 'Name already exists',
            'description.required' => 'Description is required',
            'geom_polygon.required' => 'Geometry polygon is required',
        ]
    );

    // Upload gambar jika ada
    if (!is_dir('storage/images')) {
        mkdir('storage/images', 0777, true);
    }

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
        $image->move('storage/images', $name_image);
    } else {
        $name_image = null;
    }

    // Validasi tambahan
    if (empty($request->description)) {
        return redirect()->route('map')->with('error', 'Description is required');
    }

    // Simpan ke DB
    $data = [
        'geom' => $request->geom_polygon,
        'name' => $request->name,
        'description' => $request->description,
        'image' => $name_image, // tambahkan image
    ];

    if (!$this->polygons->create($data)) {
        return redirect()->route('map')->with('error', 'Polygon failed to add');
    }

    return redirect()->route('map')->with('success', 'Polygon has been added');
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
