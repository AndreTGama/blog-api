<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Base\IndexRequest;

class IndexPostRequest extends IndexRequest
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
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],

            'order_by' => ['sometimes', 'string', 'in:created_at,title,content,views, status, published_at'],
            'order_direction' => ['sometimes', 'string', 'in:asc,desc'],

            'filters' => ['sometimes', 'array'],
            'filters.search' => ['sometimes', 'string'],
            'filters.locale' => ['sometimes', 'string', 'in:pt,en'],
        ];
    }
}
