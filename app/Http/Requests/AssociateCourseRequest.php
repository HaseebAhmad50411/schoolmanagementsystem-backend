<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssociateCourseRequest extends FormRequest
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
            'teacher_id' => 'required|array',
            'teacher_id.*' => 'exists:teachers,id',
            'student_id' => 'required|array',
            'student_id.*' => 'exists:students,id',
            'subject_id' => 'required|array',
            'subject_id.*' => 'exists:subjects,id',
        ];
    }
}
