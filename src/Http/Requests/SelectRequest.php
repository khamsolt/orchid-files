<?php

namespace Khamsolt\Orchid\Files\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @property int[] $attachments
 */
class SelectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var string $table */
        $table = config('orchid-files.table');

        return [
            'attachments' => ['required', 'array', 'filled'],
            'attachments.*' => ['required', 'numeric', Rule::exists($table, 'id')],
            'url' => ['required', 'url']
        ];
    }

    public function getFirst(): int
    {
        /** @var string[] $files */
        $files = $this->post('attachments');

        /** @var string $fileId */
        $fileId = Arr::first($files);

        return (int)$fileId;
    }

    protected function prepareForValidation(): void
    {
        !is_array($this->attachments) && $this->merge(['attachments' => [$this->attachments]]);
    }
}
