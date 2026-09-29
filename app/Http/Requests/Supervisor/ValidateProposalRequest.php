<?php

namespace App\Http\Requests\Supervisor;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class ValidateProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(Role::Supervisor);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', 'in:approved,rejected'],
            'note' => ['required_if:decision,rejected', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required_if' => 'Catatan/alasan wajib diisi jika Anda menolak (meminta revisi) proposal ini.',
        ];
    }
}
