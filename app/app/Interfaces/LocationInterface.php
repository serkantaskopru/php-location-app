<?php

namespace App\Interfaces;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

interface LocationInterface
{
    public function all(): ?Collection;
    public function find(int $id): ?Location;
    public function store(array $data): ?Location;
    public function findByCoordinate($lat, $lon): ?Location;
    public function delete(Location $location): ?bool;
}
