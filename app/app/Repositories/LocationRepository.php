<?php

namespace App\Repositories;

use App\Interfaces\LocationInterface;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @return ?int
     */
    public function update(int $id, array $data): ?int
    {
        return $this->model->whereId($id)->update($data);
    }

    /**
    /**
     * Belirli bir konum kaydını siler.
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $deleted = $this->model->whereId($id)->delete();

        if (!$deleted) {
            throw new ModelNotFoundException("Location with ID {$id} not found or already deleted.");
        }

        return true;
    }
}
