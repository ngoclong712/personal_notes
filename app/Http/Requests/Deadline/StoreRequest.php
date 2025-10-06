<?php

namespace App\Http\Requests\Deadline;

use App\Enums\DeadlinePriorityEnum;
use App\Enums\DeadlineStatusEnum;
use App\Enums\SubtaskStatusEnum;
use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
                Rule::in(DeadlinePriorityEnum::getValues()),
            ],
            'topic_id' => [
                'nullable',
                Rule::in(Topic::pluck('id')->toArray()),
            ],
            'subtasks' => [
                'required',
                'array',
                'min:1',
            ],
            'subtasks.*.title' => [
                'required',
                'string',
                'max:255',
            ],
            'subtasks.*.content' => [
                'nullable',
                'string',
            ],
            'subtasks.*.due_date' => [
                'required',
                'date',
                Rule::date()->afterOrEqual(today()),
            ],
            'subtasks.*.status' => [
                'required',
                Rule::in(SubtaskStatusEnum::getValues()),
            ]
        ];
    }
}
