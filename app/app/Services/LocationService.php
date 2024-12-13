<?php

namespace App\Services;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Repositories\LocationRepository;
use Illuminate\Support\Facades\Log;

class LocationService
{
    protected LocationRepository $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function detail($latitude, $longitude): array
    {
        try {
            $location = $this->locationRepository->findByLatLong($latitude, $longitude);

            return [
                'status' => true,
                'data' => new LocationResource($location)
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return [
                'status' => false,
                'message' => 'An error occurred while showing the location',
                'error' => $e->getMessage()
            ];
        }
    }

    public function list(): array
    {
        try {
            $locations = LocationResource::collection($this->locationRepository->all());

            return [
                'status' => true,
                'data' => $locations
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return [
                'status' => false,
                'message' => 'An error occurred while listing the locations',
                'error' => $e->getMessage()
            ];
        }
    }

    public function save(array $data): array
    {
        try {
            $location = $this->locationRepository->store($data);

            return [
                'status' => true,
                'message' => 'Location created successfully',
                'data' => new LocationResource($location)
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return [
                'status' => false,
                'message' => 'An error occurred while creating the location',
                'error' => $e->getMessage()
            ];
        }
    }

    public function update($latitude, $longitude, $data): array
    {
        try {
            $location = $this->locationRepository->findByLatLong($latitude, $longitude);

            if (!$location) {
                return [
                    'status' => false,
                    'message' => 'Location not found for the given latitude and longitude'
                ];
            }

            $location->update($data);

            return [
                'status' => true,
                'message' => 'Location updated successfully',
                'data' => new LocationResource($location)
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return [
                'status' => false,
                'message' => 'An error occurred while updating the location',
                'error' => $e->getMessage()
            ];
        }
    }

    public function destroy($latitude, $longitude): array
    {
        try {
            $location = $this->locationRepository->findByLatLong($latitude, $longitude);

            if($location){
                $this->locationRepository->delete($location);
            }

            return [
                'status' => true,
                'message' => 'Location deleted successfully',
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return [
                'status' => false,
                'message' => 'An error occurred while deleting the location',
                'error' => $e->getMessage()
            ];
        }
    }
}
