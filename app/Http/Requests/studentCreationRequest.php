<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class studentCreationRequest extends FormRequest
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
            'student_name' => 'required|string|min:5|max:50|regex:/^[a-zA-Z0-9\s\-]+$/',
            'student_email'=> 'required|email',
            'student_password'=> 'required|string',
            'student_specialty'=> 'required|string',
            'student_about' => 'required|string|min:10|max:2000|regex:/^[a-zA-Z0-9\s&\-.,()]+$/',
            'student_img' => 'required|mimes:png,jpg|max:10240',
        ];
    }
}
