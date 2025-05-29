<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PolylinesModel extends Model
{
    protected $table = 'polylines';
    protected $guarded = ['id'];

    public function geojson_polylines()
    {
        $polylines = $this->newQuery()
            ->select(DB::raw('polylines.id,
            ST_AsGeoJSON(polylines.geom) as geom,
            polylines.name,
            polylines.description,
            polylines.image,
            polylines.created_at,
            polylines.updated_at,
            polylines.user_id,
            users.name as user_created'))
            ->leftJoin('users', 'polylines.user_id', '=', 'users.id')
            ->get();

        // Struktur GeoJSON
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($polylines as $p) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom), // Konversi dari JSON
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                    'image' => $p->image,
                    'user_id'=>$p->user_id,
                    'user_created' => $p->user_created,
                ],
            ];

            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }
    public function geojson_polyline($id)
    {
        $polylines = $this->newQuery()
            ->select(DB::raw('id, ST_AsGeoJSON(geom) as geom, name, description, image, created_at, updated_at'))
            ->where('id', $id)
            ->get();

        // Struktur GeoJSON
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($polylines as $p) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom), // Konversi dari JSON
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                    'user_id'=>$p->user_id,
                    'user_created' => $p->user_created,
                    'image' => $p->image,
                ],
            ];

            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }
}
