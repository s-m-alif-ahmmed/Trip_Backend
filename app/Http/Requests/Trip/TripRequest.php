<?php

namespace App\Http\Requests\Trip;

use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('trip') ? true : false;
        $required = $isUpdate ? 'sometimes' : 'required';

        return [
            '*.departureCity.city'        => "$required|string|max:255",
            '*.departureCity.country'     => "$required|string|max:255",
            '*.departureCity.countryCode' => "$required|string|max:10",

            '*.arrivalCity.city'          => "$required|string|max:255",
            '*.arrivalCity.country'       => "$required|string|max:255",
            '*.arrivalCity.countryCode'   => "$required|string|max:10",

            '*.weight' => "$required|numeric|min:0",
            '*.date'   => "$required|date",
            '*.time'   => "$required|date_format:H:i",
        ];
    }

    public function messages(): array
    {
        return [
            '*.departure_country.required' => 'Departure country is required for each trip.',
            '*.departure_city.required'    => 'Departure city is required for each trip.',
            '*.available_weight.min'       => 'Weight must be positive.',

            '*.date.required'              => 'Date is required for each trip.',
            '*.time.required'              => 'Time is required for each trip.',
            '*.arrival_city.required'      => 'Arrival city is required for each trip.',
        ];
    }
}
