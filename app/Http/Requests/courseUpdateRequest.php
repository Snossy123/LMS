<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class courseUpdateRequest extends FormRequest
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
            'course_title' => 'required|string|min:5|max:150|regex:/^[a-zA-Z0-9\s&\-.,()]+$/',
            'course_category'=> 'required|string',
            'course_level'=> 'required|string',
            'course_language'=> 'required|string',
            'course_description' => 'required|string|min:10|max:2000|regex:/^[a-zA-Z0-9\s&\-.,()]+$/',
            'course_details' => 'required|string|min:20|max:2000|regex:/^[a-zA-Z0-9\s&\-.,()]+$/',
            'course_img' => 'nullable|mimes:png,jpg|max:10240',
        ];
    }
}
