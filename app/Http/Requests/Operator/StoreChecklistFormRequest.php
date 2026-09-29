<?php

namespace App\Http\Requests\Operator;

use App\Enums\ChecklistItemKind;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChecklistFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(Role::Operator, Role::SuperOperator);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:checklist_items,id'],
            'items.*.code' => ['required', 'string', 'max:30'],
            'items.*.label' => ['required', 'string', 'max:500'],
            'items.*.kind' => ['required', 'string', Rule::in(array_column(ChecklistItemKind::cases(), 'value'))],
        ];
    }

    public function attributes(): array
    {
        return [
            'items' => 'Item Checklist',
            'items.*.code' => 'Kode Item',
            'items.*.label' => 'Label Item',
            'items.*.kind' => 'Jenis Item',
        ];
    }
}
