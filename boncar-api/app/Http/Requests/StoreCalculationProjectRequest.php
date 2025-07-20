<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCalculationProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'land_area' => 'required|numeric|min:0',
            // Aturan baru untuk sample_area
            'sample_area' => 'nullable|numeric|min:0|required_if:method,sampling',
            'method' => ['required', Rule::in(['census', 'sampling'])],
            'trees' => 'required|array|min:1',
            'trees.*.species_id' => 'required|exists:species,id',
            'trees.*.allometric_equation_id' => 'required|exists:allometric_equations,id',
            'trees.*.parameters' => 'required|array',
            'trees.*.parameters.circumference' => 'required|numeric|min:0',
            'trees.*.parameters.height' => 'nullable|numeric|min:0',
            'trees.*.parameters.wood_density' => 'nullable|numeric|min:0',
        ];
    }
}