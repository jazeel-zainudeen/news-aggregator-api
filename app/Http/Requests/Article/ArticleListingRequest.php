<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ArticleListingRequest extends FormRequest
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
            'search' => 'nullable|string|max:255',
            'date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'category' => 'nullable|integer|exists:categories,id',
            'source' => 'nullable|integer|exists:sources,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
