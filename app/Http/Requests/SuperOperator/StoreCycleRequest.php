<?php

namespace App\Http\Requests\SuperOperator;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(Role::SuperOperator);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'max_proposals_per_supervisor' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'year' => 'Tahun',
            'name' => 'Nama Siklus',
            'max_proposals_per_supervisor' => 'Maks Proposal per Dosen',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
