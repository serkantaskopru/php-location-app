<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 * @property string $color
 */
class LocationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'color' => $this->color,
        ];
    }
}
