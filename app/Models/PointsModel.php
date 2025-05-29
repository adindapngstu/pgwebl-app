<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointsModel extends Model
{
    protected $table = 'points';
    protected $guarded = ['id'];

    public function geojson_points()
    {
        $points = $this->newQuery()
            ->select(DB::raw('points.id,
            ST_AsGeoJSON(points.geom) as geom,
            points.name,
            points.description,
            points.image,
            points.created_at,
            points.updated_at,
            points.user_id,
            users.name as user_created'))
            ->LeftJoin('users', 'points.user_id', '=', 'users.id')
            ->get();

        // Struktur GeoJSON
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($points as $p) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom),
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at,
                    'image' => $p->image,
                    'user_created' => $p->user_created,
                    'user_id' => $p->user_id,
                ],
            ];

            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }

    public function geojson_point($id)
    {
        $points = $this->newQuery()
            ->select(DB::raw('id, ST_AsGeoJSON(geom) as geom, name, description, image, created_at, updated_at'))
            ->leftjoin('users', 'points.user_id', '=', 'users.id')
            ->where('id', $id)
            ->get();

        // Struktur GeoJSON
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($points as $p) {
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
