<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationDestroyRequest extends FormRequest
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
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'location' => $this->route('location'),
        ]);
    }

}
