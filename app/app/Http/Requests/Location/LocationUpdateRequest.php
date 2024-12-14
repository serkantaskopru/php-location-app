<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class LocationUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'location' => [
                'required',
                'integer',
                'exists:locations,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            'color' => [
                'required',
                'string',
                'size:7',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'location' => $this->route('location'),
        ]);
    }
}
