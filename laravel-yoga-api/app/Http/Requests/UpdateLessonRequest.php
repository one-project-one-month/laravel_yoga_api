<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
        ];
    }
}