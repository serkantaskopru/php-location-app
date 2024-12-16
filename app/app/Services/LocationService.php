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
        $route = $this->getRouteByLatLong($latitude, $longitude);

        return [
            'status' => true,
            'data' => LocationRouteResource::collection($route)
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

    /**
     * Verilen koordinatlara (latitude, longitude) göre en yakın konumları mesafeye göre sıralayarak döndürür.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function getRouteByLatLong(float $latitude, float $longitude): array
    {
        $visited = [];
        $remainingPoints = $this->locationRepository->all();

        // Başlangıç noktası için belirttiğiniz konuma en yakın noktayı bul.
        $currentPoint = $remainingPoints
            ->sortBy(fn($point) => $this->calculateDistance($latitude, $longitude, $point->latitude, $point->longitude))
            ->shift();

        // Eğer başlangıç noktası bulunamazsa boş döndür.
        if (!$currentPoint) {
            return [];
        }

        // Rotaya başlangıç noktasını ekle ve ziyaret edilenlere işaretle.

        $route = [$currentPoint];
        $visited[$currentPoint->id] = true;

        // Kalan tüm noktalar için en yakın komşuyu bul.
        while (count($remainingPoints) > 0) {
            // Ziyaret edilen noktayı listeden çıkar.
            $remainingPoints = $remainingPoints->reject(fn($point) => isset($visited[$point->id]));

            // Sıradaki noktayı işleme al
            $nextPoint = $remainingPoints
                ->sortBy(fn($point) => $this->calculateDistance(
                    $currentPoint->latitude,
                    $currentPoint->longitude,
                    $point->latitude,
                    $point->longitude
                ))
                ->first();

            if (!$nextPoint) {
                break; // Eğer bir sonraki nokta yoksa döngüyü sonlandır.
            }

            // Rotaya bir sonraki noktayı ekle ve güncelle.
            $route[] = $nextPoint;
            $visited[$nextPoint->id] = true;
            $currentPoint = $nextPoint;
        }

        return $route;
    }

    /**
     * İki nokta arasındaki mesafeyi hesaplar.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Mesafe (metre cinsinden)
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Dünya yarıçapı (metre cinsinden)

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
