<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\LocationDestroyRequest;
use App\Http\Requests\Location\LocationGetRouteListRequest;
use App\Http\Requests\Location\LocationListRequest;
use App\Http\Requests\Location\LocationStoreRequest;
use App\Http\Requests\Location\LocationUpdateRequest;
use App\Http\Requests\Location\LocationViewRequest;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    // LocationService sınıfı üzerinden işlemleri yöneten bir özellik tanımlanıyor
    protected LocationService $locationService;

    // Controller'ın constructor'ı ile LocationService inject ediliyor
    public function __construct(LocationService $locationService){
        $this->locationService = $locationService;
    }

    /**
     * Konumların listesini döner.
     *
     * @param LocationListRequest $request - Listeleme için gerekli doğrulama kuralları
     * @return JsonResponse - Listeleme sonucunu döner
     */
    public function index(LocationListRequest $request): JsonResponse
    {
        $result = $this->locationService->list();

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Yeni bir konum oluşturur.
     *
     * @param LocationStoreRequest $request - Konum oluşturmak için gerekli doğrulama kuralları
     * @return JsonResponse - Oluşturma sonucunu döner
     */
    public function store(LocationStoreRequest $request): JsonResponse
    {
        $result = $this->locationService->save($request->validated());

        return response()->json($result, $result['status'] ? 201 : 500);
    }

    /**
     * Gönderilen koordinatlara göre konumları mesafeye göre sıralar.
     *
     * @param LocationGetRouteListRequest $request - Mesafeye göre sıralama için gerekli doğrulama kuralları
     * @return JsonResponse - Sıralama sonucunu döner
     */
    public function getRouteList(LocationGetRouteListRequest $request): JsonResponse
    {
        $result = $this->locationService->getRouteList($request->latitude, $request->longitude);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Belirtilen konumun detaylarını döner.
     *
     * @param LocationViewRequest $request - Konum detayını almak için gerekli doğrulama kuralları
     * @param int $location - Konumun ID'si
     * @return JsonResponse - Detay sonucunu döner
     */
    public function show(LocationViewRequest $request, $location): JsonResponse
    {
        $result = $this->locationService->detail($location);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Mevcut bir konumu günceller.
     *
     * @param LocationUpdateRequest $request - Güncelleme için gerekli doğrulama kuralları
     * @param int $location - Güncellenecek konumun ID'si
     * @return JsonResponse - Güncelleme sonucunu döner
     */
    public function update(LocationUpdateRequest $request, $location): JsonResponse
    {
        $data = $request->only(['name', 'color', 'latitude', 'longitude']);

        $result = $this->locationService->update($location, $data);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Belirtilen bir konumu siler.
     *
     * @param LocationDestroyRequest $request - Silme işlemi için gerekli doğrulama kuralları
     * @param int $location - Silinecek konumun ID'si
     * @return JsonResponse - Silme sonucunu döner
     */
    public function destroy(LocationDestroyRequest $request, $location): JsonResponse
    {
        $result = $this->locationService->destroy($location);

        return response()->json($result, $result['status'] ? 200 : 500);
    }
}
