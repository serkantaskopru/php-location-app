<?php

namespace App\Services;

use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationRouteResource;
use App\Repositories\LocationRepository;

class LocationService
{
    protected LocationRepository $locationRepository;

    // Constructor injection ile LocationRepository bağımlılığı.
    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Belirtilen ID'ye sahip bir konumun detaylarını getirir.
     *
     * @param int $id - Konum ID'si
     * @return array - Konumun detaylarını dizi olarak döner
     */
    public function detail(int $id): array
    {
        $location = $this->locationRepository->find($id);

        return [
            'status' => true,
            'data' => new LocationResource($location)
        ];
    }

    /**
     * Tüm konumların listesini döner.
     *
     * @return array - Konumların listesini dizi olarak döner
     */
    public function list(): array
    {
        $locations = LocationResource::collection($this->locationRepository->all());

        return [
            'status' => true,
            'data' => $locations
        ];
    }

    /**
     * Verilen enlem ve boylam değerine göre bir rota oluşturur.
     *
     * @param float $latitude - Başlangıç noktası için enlem
     * @param float $longitude - Başlangıç noktası için boylam
     * @return array - Rotayı içeren dizi olarak döner
     */
    public function getRouteList(float $latitude, float $longitude): array
    {
        $route = $this->getRouteByLatLong($latitude, $longitude);

        return [
            'status' => true,
            'data' => LocationRouteResource::collection($route)
        ];
    }

    /**
     * Yeni bir konum kaydeder.
     *
     * @param array $data - Kaydedilecek konum verileri
     * @return array - Kaydedilen konumun detaylarını dizi olarak döner.
     */
    public function save(array $data): array
    {
        $location = $this->locationRepository->store($data);

        return [
            'status' => true,
            'message' => 'Location created successfully',
            'data' => new LocationResource($location)
        ];
    }

    /**
     * Belirtilen ID'ye sahip bir konumu günceller.
     *
     * @param int $id - Güncellenecek konumun ID'si
     * @param array $data - Güncelleme verileri
     * @return array - Güncelleme işleminin sonucunu dizi olarak döner.
     */
    public function update(int $id, array $data): array
    {
        $this->locationRepository->update($id, $data);

        return [
            'status' => true,
            'message' => 'Location updated successfully'
        ];
    }

    /**
     * Belirtilen ID'ye sahip bir konumu siler.
     *
     * @param int $id - Silinecek konumun ID'si
     * @return array - Silme işleminin sonucunu dizi olarak döner.
     */
    public function destroy(int $id): array
    {
        $this->locationRepository->delete($id);

        return [
            'status' => true,
            'message' => 'Location deleted successfully',
        ];
    }

    /**
     * Bu method, verilen bir konuma en yakın noktadan başlayıp bitiş noktasına kadar birbirine yakın her iki noktayı
     * rota dizisine kayıt eder ve oluşan rotayı döndürür
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
