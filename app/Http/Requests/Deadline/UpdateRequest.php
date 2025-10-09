<?php

namespace App\Http\Requests\Deadline;

use App\Enums\DeadlinePriorityEnum;
use App\Enums\SubtaskStatusEnum;
use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'priority' => [
                'required',
                'integer',
                Rule::in(DeadlinePriorityEnum::getValues()),
            ],
            'topic_id' => [
                'nullable',
                'integer',
                Rule::in(Topic::pluck('id')->toArray()),
            ],
            'subtasks' => [
                'required',
                'array',
                'min:1',
            ],
            'subtasks.*.id' => [
                'sometimes',
                'exists:subtasks,id',
            ],
            'subtasks.*.title' => [
                'required_with:subtasks',
                'string',
                'max:255',
            ],
            'subtasks.*.content' => [
                'nullable',
                'string',
            ],
            'subtasks.*.due_date' => [
                'required_with:subtasks',
                'date',
            ],
            'subtasks.*.status' => [
                'required_with:subtasks',
                'integer',
                Rule::in(SubtaskStatusEnum::getValues()),
            ]
        ];
    }
}
