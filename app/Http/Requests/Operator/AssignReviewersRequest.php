<?php

namespace App\Http\Requests\Operator;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class AssignReviewersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(Role::Operator, Role::SuperOperator);
    }

    public function rules(): array
    {
        return [
            'admin_reviewer_id' => ['required', 'exists:users,id'],
            'substantive_reviewer_id_1' => ['required', 'exists:users,id', 'different:admin_reviewer_id'],
            'substantive_reviewer_id_2' => ['required', 'exists:users,id', 'different:admin_reviewer_id', 'different:substantive_reviewer_id_1'],
            'due_at' => ['required', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'different' => 'Ketiga reviewer (1 Admin, 2 Substantif) harus merupakan orang yang berbeda.',
            'due_at.after' => 'Batas waktu harus di masa depan.',
        ];
    }
}
