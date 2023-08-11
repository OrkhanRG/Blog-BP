<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required','max:255'],
            'slug' => ['max:255', 'unique:articles,slug,' . $this->id],
            'body' => ['required'],
            'category_id' => ['required'],
//            'image' => ['nullable', 'image', 'mimes:png,jpeg', 'max:2048'],
            'image' => ['image', 'mimetypes:image/png,image/jpeg,image/jpg', 'max:2048']
        ];
    }
}
