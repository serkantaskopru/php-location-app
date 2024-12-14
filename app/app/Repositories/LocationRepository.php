<?php

namespace App\Repositories;

use App\Interfaces\LocationInterface;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository implements LocationInterface
{
    protected Location $model;

    public function __construct(Location $model)
    {
        $this->model = $model;
    }

    /**
     * Tüm konum kayıtlarını döndürür.
     *
     * @return Collection|null
     */
    public function all(): ?Collection
    {
        return $this->model->all();
    }

    /**
     * Belirli bir ID'ye sahip konumu döndürür.
     *
     * @param int $id
     * @return Location|null
     */
    public function find(int $id): ?Location
    {
        return $this->model->find($id);
    }

    /**
     * Yeni bir konum kaydı oluşturur ve döndürür.
     *
     * @param array $data
     * @return Location|null
     */
    public function store(array $data): ?Location
    {
        return $this->model->create($data);
    }

    /**
     * Belirli bir ID'ye sahip konum kaydını günceller.
     *
     * @param int $id
     * @param array $data
     * @return Location
     */
    public function update($id, array $data): Location
    {
        $location = $this->model->findOrFail($id);

        $location->update($data);

        return $location;
    }

    /**
     * Belirli bir konum kaydını siler.
     *
     * @param Location $location
     * @return bool|null
     */
    public function delete(Location $location): ?bool
    {
        return $location->delete();
    }

    /**
     * Verilen koordinatlara (latitude, longitude) göre en yakın konumları mesafeye göre sıralayarak döndürür.
     *
     * @param float $latitude
     * @param float $longitude
     * @return \Illuminate\Support\Collection
     */
    public function getRouteList($latitude, $longitude): \Illuminate\Support\Collection
    {
        return $this->model->selectRaw("
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
