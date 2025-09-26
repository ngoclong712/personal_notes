<?php

namespace App\Http\Requests\Note;

use App\Enums\NoteStatusEnum;
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
                Rule::unique('notes', 'title')->ignore($this->route('note')?->id),
                'max:256',
            ],
            'content' => [
                'required',
            ],
            'topic_id' => [
                'nullable',
                'integer',
                Rule::exists('topics', 'id'),
            ],
            'status' => [
                'required',
                'integer',
                Rule::in(NoteStatusEnum::getValues()),
            ]
        ];
    }
}
