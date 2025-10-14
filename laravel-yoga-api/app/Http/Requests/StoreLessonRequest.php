<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
        ];
    }
}