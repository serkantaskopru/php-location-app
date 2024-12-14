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
    protected LocationService $locationService;

    public function __construct(LocationService $locationService){
        $this->locationService = $locationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(LocationListRequest $request): JsonResponse
    {
        $result = $this->locationService->list();

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationStoreRequest $request): JsonResponse
    {
        $result = $this->locationService->save($request->validated());

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Order locations by distance
     */
    public function getRouteList(LocationGetRouteListRequest $request): JsonResponse
    {
        $result = $this->locationService->getRouteList($request->latitude, $request->longitude);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(LocationViewRequest $request, $location): JsonResponse
    {
        $result = $this->locationService->detail($location);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationUpdateRequest $request, $location): JsonResponse
    {
        $data = $request->only(['name', 'color', 'latitude', 'longitude']);

        $result = $this->locationService->update($location, $data);

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocationDestroyRequest $request, $location): JsonResponse
    {
        $result = $this->locationService->destroy($location);

        return response()->json($result, $result['status'] ? 200 : 500);
    }
}
