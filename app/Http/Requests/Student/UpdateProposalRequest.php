<?php

namespace App\Http\Requests\Student;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(Role::Student);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'scheme_id' => ['required', 'integer', 'exists:schemes,id'],
            'theme_id' => ['nullable', 'integer', 'exists:themes,id'],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'admin_cost_amount' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul Proposal',
            'scheme_id' => 'Skema',
            'theme_id' => 'Tema',
            'supervisor_id' => 'Dosen Pendamping',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Selesai',
            'admin_cost_amount' => 'Biaya Administrasi',
        ];
    }
}
