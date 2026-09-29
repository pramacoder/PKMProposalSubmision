<?php

namespace App\Http\Requests\Operator;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreRubricCriterionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(Role::Operator, Role::SuperOperator);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*.id' => ['nullable', 'integer', 'exists:rubric_criteria,id'],
            'criteria.*.group_label' => ['nullable', 'string', 'max:255'],
            'criteria.*.label' => ['required', 'string', 'max:500'],
            'criteria.*.weight' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'criteria' => 'Kriteria',
            'criteria.*.label' => 'Label Kriteria',
            'criteria.*.weight' => 'Bobot',
            'criteria.*.group_label' => 'Grup Kriteria',
        ];
    }
}
