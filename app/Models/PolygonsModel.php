<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PolygonsModel extends Model
{
    protected $table = 'polygons';
    protected $guarded = ['id'];

    public function geojson_polygons()
    {
        $polygons = $this->newQuery()
            ->select(DB::raw('ST_AsGeoJSON(geom) as geom, name, description, st_area(geom)/1000000 as area_ha, image, created_at, updated_at'))
            ->get();

        // Struktur GeoJSON
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($polygons as $p) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom), // Konversi dari JSON
                'properties' => [
                    'name' => $p->name,
                    'description' => $p->description,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                    'image' => $p->image,
                ],
            ];

            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }
}
