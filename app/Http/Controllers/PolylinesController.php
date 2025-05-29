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

    public function store(Request $request)
    {
        // Validasi input (seragam dengan controller lainnya)
        $request->validate(
            [
                'name' => 'required|unique:polylines,name',
                'description' => 'required',
                'geom_polyline' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2000',
            ],
            [
                'name.required' => 'Polyline name is required',
                'name.unique' => 'Polyline name already exists',
                'description.required' => 'Description is required',
                'geom_polyline.required' => 'Geometry polyline is required',
            ]
        );

        // Buat direktori gambar jika belum ada
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        // Data untuk disimpan
        $data = [
            'geom' => $request->geom_polyline,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
            'user_id'=>auth()->user()->id,
        ];

        // Simpan ke DB
        if (!$this->polylines->create($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to add');
        }

        return redirect()->route('map')->with('success', 'Polyline has been added');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polyline',
            'id' => $id,
        ];

        return view('edit_polyline', $data);
    }

    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate(
            [
                'name' => 'required|unique:polylines,name,' . $id,
                'description' => 'required',
                'geom_polyline' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2000',
            ],
            [
                'name.required' => 'Polyline name is required',
                'name.unique' => 'Polyline name already exists',
                'description.required' => 'Description is required',
                'geom_polyline.required' => 'Geometry polyline is required',
            ]
        );

        //Create image directory
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);}

        // Get old image file
        $old_image = $this->polylines->find($id)->image;

         //Get Image file
    if ($request->hasFile('image')) {
            $image = $request->File('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);

            //Delete old image file
            if ($old_image != null) {
                if (file_exists('./storage/images/' . $old_image)) {
                    unlink('./storage/images/' . $old_image);
                }
            }
            } else {
            $name_image = $old_image;
        }

        // Cek apakah description kosong
        if (empty($request->description)) {
            return redirect()->route('map')->with('error', 'Description is required');
        }

        $data = [
            'geom' => $request->geom_polyline,
            'name' => $request->name,
            'description' => $request->description,
            'image'=> $name_image,
        ];

        // Update data
        if (!$this->polylines->find($id)->update($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to update');
        }

        //Redirect ke halaman peta
        return redirect()->route('map')->with('success', 'Polyline has been updated');
    }

    public function destroy(string $id)
    {
        $imagefile = $this->polylines->find($id)->image;

        if (!$this->polylines->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polyline failed to delete');
        } else {
            // Hapus file gambar jika ada
            if ($imagefile != null) {
                $imagePath = './storage/images/' . $imagefile;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            return redirect()->route('map')->with('success', 'Polyline has been deleted');
        }
    }
}
