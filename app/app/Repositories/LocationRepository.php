<?php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LocationRepository
{
    protected Location $model;

    public function __construct(Location $model)
    {
        $this->model = $model;
    }

    public function all(): ?Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Location
    {
        return $this->model->find($id);
    }

    public function findByLatLong($lat, $lon): ?Location
    {
        return $this->model->where('latitude', $lat)->where('longitude', $lon)->first();
    }

    public function store(array $data): ?Location
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): Location
    {
        $location = $this->model->findOrFail($id);

        $location->update($data);

        return $location;
    }

    public function delete(Location $location): ?bool
    {
        return $location->delete();
    }

    public function getRouteList($latitude, $longitude): \Illuminate\Support\Collection
    {
        return DB::table('locations')
            ->selectRaw("
                *,
                (
                    (ACOS(
                        SIN(? * PI() / 180) * SIN(`latitude` * PI() / 180) +
                        COS(? * PI() / 180) * COS(`latitude` * PI() / 180) *
                        COS((? - `longitude`) * PI() / 180)
                    ) * 180 / PI()) * 60 * 1.1515 * 1.609344
                ) AS `distance`
            ", [$latitude, $latitude, $longitude])
            ->orderBy('distance', 'asc')
            ->get();
    }
}
