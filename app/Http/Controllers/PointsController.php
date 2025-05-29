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
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2000', // Validasi untuk gambar
            ],
            [
                'name.required' => 'Point name is required',
                'name.unique' => 'Point name already exists',
                'description.required' => 'Description is required',
                'geom_point.required' => 'Geometry point is required',
            ]
        );

        //Create image directory
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);}

         //Get Image file
    if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_point." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
            } else {
            $name_image = null;}

        // Cek apakah description kosong
        if (empty($request->description)) {
            return redirect()->route('map')->with('error', 'Description is required');
        }

        $data = [
            'geom' => $request->geom_point,
            'name' => $request->name,
            'description' => $request->description,
            'image'=> $name_image,
            'user_id'=>auth()->user()->id,
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
        $data = [
            'title' => 'Edit Point',
            'id' => $id,
        ];

        return view('edit_point', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //dd($id, $request->all());
        // Validasi input (kembalikan validasi 'description' agar tetap wajib diisi)
        $request->validate(
            [
                'name' => 'required|unique:points,name, ' . $id,
                'description' => 'required', // Description kembali menjadi wajib
                'geom_point' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2000', // Validasi untuk gambar
            ],
            [
                'name.required' => 'Point name is required',
                'name.unique' => 'Point name already exists',
                'description.required' => 'Description is required',
                'geom_point.required' => 'Geometry point is required',
            ]
        );

        //Create image directory
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);}

        // Get old image file
        $old_image = $this->points->find($id)->image;

         //Get Image file
    if ($request->hasFile('image')) {
            $image = $request->File('image');
            $name_image = time() . "_point." . strtolower($image->getClientOriginalExtension());
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
            'geom' => $request->geom_point,
            'name' => $request->name,
            'description' => $request->description,
            'image'=> $name_image,
        ];

        // Update data
        if (!$this->points->find($id)->update($data)) {
            return redirect()->route('map')->with('error', 'Point failed to update');
        }

        //Redirect ke halaman peta
        return redirect()->route('map')->with('success', 'Point has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagefile = $this->points->find($id)->image;

        if (!$this->points->destroy($id)) {
            return redirect()->route('map')->with('error', 'Point failed to delete');
        } else {

            //Delete image file
            if ($imagefile != null) {
                if (file_exists('./storage/images/' . $imagefile)) { {
                    unlink('./storage/images/' . $imagefile);
                }
            }
            return redirect()->route('map')->with('success', 'Point has been deleted');
        }
    }
}
}
