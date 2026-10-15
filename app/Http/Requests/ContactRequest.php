<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'first_name' =>['required','string','max:255',],
            'last_name' =>['required','string','max:255',],
            'gender' =>['required', 'in:1,2,3',],
            'email' =>['required','email', 'max:255'],
            'tel' =>['required', 'regex:/^[0-9]{1,11}$/'],
            'building'    => ['nullable', 'string', 'max:255'],
            'address' =>['required', 'string', 'max:255'],
            'category_id' =>['required',],
            'detail' =>['required',],
            'tag_ids'     => ['nullable', 'array'],
            'tag_ids.*'   => ['exists:tags,id'],
        ];
    }
}
