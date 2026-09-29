<?php

namespace App\Http\Requests\Student;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreProposalFundingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(Role::Student);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'amounts' => ['required', 'array'],
            'amounts.belmawa' => ['nullable', 'integer', 'min:0'],
            'amounts.university' => ['nullable', 'integer', 'min:0'],
            'amounts.partner' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
