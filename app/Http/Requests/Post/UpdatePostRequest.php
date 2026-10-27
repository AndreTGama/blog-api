<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Post\Concerns\ValidatesPostContent;
use Illuminate\Http\UploadedFile;

class UpdatePostRequest extends FormRequest
{
    use ValidatesPostContent;

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
            'cover_image' => $this->file('cover_image') instanceof UploadedFile
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']
                : ['nullable', 'string', 'url', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,published,archived'],
            'allow_comments' => ['sometimes', 'boolean'],

            'post_metas' => ['nullable', 'array'],
            'post_metas.*.key' => ['required_with:post_metas', 'string', 'max:255'],
            'post_metas.*.value' => ['required_with:post_metas', 'string'],

            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['string', 'exists:categories,id'],
            ...$this->contentRules(required: false),
        ];
    }
}
