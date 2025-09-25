<?php

namespace App\Http\Requests\Note;

use App\Enums\NoteStatusEnum;
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
                'max:256',
                'unique:notes,title',
            ],
            'content' => [
                'required',
            ],
            'topic_id' => [
                'nullable',
                Rule::in(Topic::pluck('id')->toArray()),
                'integer',
            ],
            'status' => [
                'required',
                Rule::in(NoteStatusEnum::getValues()),
            ]
        ];
    }
}
