<?php

namespace App\Http\Requests\Student;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(Role::Student);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'scheme_id' => ['required', 'integer', 'exists:schemes,id'],
            'title' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'scheme_id' => 'Skema PKM',
            'title' => 'Judul Proposal',
        ];
    }
}
