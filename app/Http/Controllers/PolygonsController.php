<?php

namespace App\Http\Controllers;

use App\Models\PolygonsModel;
use Illuminate\Http\Request;

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
        // Validasi input (serupa dengan PointsController)
        $request->validate(
            [
                'name' => 'required|unique:polygons,name',
                'description' => 'required',
                'geom_polygon' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2000',
            ],
            [
                'name.required' => 'Polygon name is required',
                'name.unique' => 'Polygon name already exists',
                'description.required' => 'Description is required',
                'geom_polygon.required' => 'Geometry polygon is required',
            ]
        );

        // Buat folder penyimpanan jika belum ada
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }

        // Ambil file gambar jika tersedia
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        // Siapkan data untuk disimpan
        $data = [
            'geom' => $request->geom_polygon,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
            'user_id'=>auth()->user()->id,
        ];

        // Simpan ke database
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
        $data = [
            'title' => 'Edit Polygon',
            'id' => $id,
        ];

        return view('edit_polygon', $data);
    }

    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate(
            [
                'name' => 'required|unique:polygons,name, ' . $id,
                'description' => 'required',
                'geom_polygon' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2000',
            ],
            [
                'name.required' => 'Polygon name is required',
                'name.unique' => 'Polygon name already exists',
                'description.required' => 'Description is required',
                'geom_polygon.required' => 'Geometry polygon is required',
            ]
        );

        //create image directory
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }
        // Cek apakah deskripsi kosong
        if (empty($request->description)) {
            return redirect()->route('map')->with('error', 'Description is required');
        }
        // Cek apakah geometri polygon kosong
        if (empty($request->geom_polygon)) {
            return redirect()->route('map')->with('error', 'Geometry polygon is required');
        }
        // Cek apakah nama polygon kosong
        if (empty($request->name)) {
            return redirect()->route('map')->with('error', 'Polygon name is required');
        }
        // Cek apakah nama polygon sudah ada
        if ($this->polygons->where('name', $request->name)->exists()) {
            return redirect()->route('map')->with('error', 'Polygon name already exists');
        }
        // Cek apakah file gambar ada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            // Jika tidak ada file gambar, ambil nama gambar yang sudah ada
            $name_image = $this->polygons->find($id)->image;
        }
        // Hapus gambar lama jika ada
        $old_image = $this->polygons->find($id)->image;
        if ($old_image != null) {
            $imagePath = './storage/images/' . $old_image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }else {
            $name_image = $old_image;
        }

        // Siapkan data untuk update
        $data = [
            'geom' => $request->geom_polygon,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
        ];

        // Perbarui data di database
        if (!$this->polygons->find($id)->update($data)) {
            return redirect()->route('map')->with('error', 'Polygon failed to update');
        }

        return redirect()->route('map')->with('success', 'Polygon has been updated');
    }

    public function destroy(string $id)
    {
        $imagefile = $this->polygons->find($id)->image;

        if (!$this->polygons->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polygon failed to delete');
        } else {
            // Hapus file gambar jika ada
            if ($imagefile != null) {
                $imagePath = './storage/images/' . $imagefile;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            return redirect()->route('map')->with('success', 'Polygon has been deleted');
        }
    }
}
