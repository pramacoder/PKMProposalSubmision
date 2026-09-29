<?php

namespace App\Http\Requests\Operator;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePhaseWindowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(Role::Operator, Role::SuperOperator);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after_or_equal:opens_at'],
            'forced_open' => ['boolean'],
            'forced_closed' => ['boolean'],
            'forced_reason' => ['nullable', 'string', 'max:500',
                'required_if:forced_open,true',
                'required_if:forced_closed,true',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'opens_at' => 'Tanggal Buka',
            'closes_at' => 'Tanggal Tutup',
            'forced_reason' => 'Alasan Paksa',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'forced_open' => $this->boolean('forced_open'),
            'forced_closed' => $this->boolean('forced_closed'),
        ]);
    }
}
