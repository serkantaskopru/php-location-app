<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'color' => [
                'required',
                'string',
                'size:7',
            ],
            'latitude' => [
                'required',
                'numeric',
            ],
            'longitude' => [
                'required',
                'numeric',
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
