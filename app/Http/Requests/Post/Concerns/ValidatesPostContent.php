<?php

namespace App\Http\Requests\Post\Concerns;

trait ValidatesPostContent
{
    /**
     * Validate either legacy text content or an ordered list of content blocks.
     *
     * @return array<string, array<mixed>|string>
     */
    protected function contentRules(bool $required): array
    {
        $presenceRules = $required ? ['required'] : ['sometimes', 'nullable'];

        if (! is_array($this->input('content'))) {
            return ['content' => [...$presenceRules, 'string']];
        }

        return [
            'content' => [...$presenceRules, 'array', 'list', 'min:1'],
            'content.*' => ['required', 'array'],
            'content.*.type' => ['required', 'string', 'in:paragraph,code_tabs'],
            'content.*.text' => [
                'required_if:content.*.type,paragraph',
                'prohibited_unless:content.*.type,paragraph',
                'string',
            ],
            'content.*.tabs' => [
                'required_if:content.*.type,code_tabs',
                'prohibited_unless:content.*.type,code_tabs',
                'array',
                'min:1',
            ],
            'content.*.tabs.*' => ['required', 'array'],
            'content.*.tabs.*.label' => ['required', 'string', 'max:100'],
            'content.*.tabs.*.language' => ['required', 'string', 'max:50'],
            'content.*.tabs.*.code' => ['required', 'string'],
        ];
    }
}
