<?php

namespace App\Http\Requests\SuperOperator;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(Role::SuperOperator);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nim' => ['nullable', 'string', 'max:30'],
            'nidn' => ['nullable', 'string', 'max:20'],
            'nuptk' => ['nullable', 'string', 'max:20'],
            'study_program' => ['nullable', 'string', 'max:255'],
            'cohort_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::in(array_column(Role::cases(), 'value'))],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'email' => 'Email Student',
            'nim' => 'NIM',
            'nidn' => 'NIDN',
            'nuptk' => 'NUPTK',
            'study_program' => 'Program Studi',
            'cohort_year' => 'Tahun Angkatan',
            'roles' => 'Peran',
        ];
    }
}
