<?php

namespace Khamsolt\Orchid\Files\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SelectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attachments' => ['required', 'array', 'filled'],
            'attachments.*' => ['required', 'numeric', Rule::exists(config('orchid-files.table'), 'id')],
            'url' => ['required', 'url']
        ];
    }

    public function getFirst(): ?int
    {
        $files = $this->post('attachments');

        return (int)Arr::first($files);
    }

    protected function prepareForValidation(): void
    {
        !is_array($this->attachments) && $this->merge(['attachments' => [$this->attachments]]);
    }
}
