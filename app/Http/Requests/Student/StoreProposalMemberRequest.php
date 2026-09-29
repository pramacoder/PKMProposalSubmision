<?php

namespace App\Http\Requests\Student;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreProposalMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(Role::Student);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // Dalam implementasi nyata, kita sebaiknya memeriksa user ini valid dan role Student.
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'Mahasiswa',
            'role' => 'Peran',
        ];
    }
}
