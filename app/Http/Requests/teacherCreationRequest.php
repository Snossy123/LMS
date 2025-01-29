<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class teacherCreationRequest extends FormRequest
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
            'teacher_name' => 'required|string|min:5|max:50|regex:/^[a-zA-Z0-9\s\-]+$/',
            'teacher_email'=> 'required|email',
            'teacher_password'=> 'required|string',
            'teacher_specialty'=> 'required|string',
            'teacher_about' => 'required|string|min:10|max:2000|regex:/^[a-zA-Z0-9\s&\-.,()]+$/',
            'teacher_img' => 'required|mimes:png,jpg|max:10240',
        ];
    }
}
