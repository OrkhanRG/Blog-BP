<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
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
            'name' => ['required','max:255'],
            'slug' => ['max:255'],
            'description' => ['max:255'],
            'seo_description' => ['max:255'],
            'seo_keywords' => ['max:255'],
            'image' => ['image', 'mimetypes:image/png,image/jpeg,image/jpg', 'max:2048', 'nullable']
        ];
    }

    public function messages()
    {
        return [
            'name.reqired' => 'Kateqoriya adı mütləq girilməlidir!',
            'name.max' => 'Kateqoriya Adı 255 simvoldan az olmalıdır!',
            'slug.max' => 'Kateqoriya Slug 255 simvoldan az olmalıdır!',
            'description.max' => 'Kateqoriya Description 255 simvoldan az olmalıdır!',
            'seo_description.max' => 'Kateqoriya Seo Description 255 simvoldan az olmalıdır!',
            'seo_keywords.max' => 'Kateqoriya Seo Keywords 255 simvoldan az olmalıdır!',
        ];
    }
}
