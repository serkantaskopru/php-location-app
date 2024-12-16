<?php

namespace App\Services;

use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationRouteResource;
use App\Repositories\LocationRepository;

class LocationService
{
    protected LocationRepository $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function detail($id): array
    {
        $location = $this->locationRepository->find($id);

        return [
            'status' => true,
            'data' => new LocationResource($location)
        ];
    }

    public function list(): array
    {
        $locations = LocationResource::collection($this->locationRepository->all());

        return [
            'status' => true,
            'data' => $locations
        ];
    }

    public function getRouteList($latitude, $longitude): array
    {
        $locations = LocationRouteResource::collection($this->locationRepository->getRouteList($latitude, $longitude));

        return [
            'status' => true,
            'data' => $locations
        ];
    }

    public function save(array $data): array
    {
        $location = $this->locationRepository->store($data);

        return [
            'status' => true,
            'message' => 'Location created successfully',
            'data' => new LocationResource($location)
        ];
    }

    public function update($id, $data): array
    {
        $this->locationRepository->update($id, $data);

        return [
            'status' => true,
            'message' => 'Location updated successfully'
        ];
    }

    public function destroy($id): array
    {
        $this->locationRepository->delete($id);

        return [
            'status' => true,
            'message' => 'Location deleted successfully',
        ];
    }
}
