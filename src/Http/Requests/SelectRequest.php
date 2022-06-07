<?php

namespace Khamsolt\Orchid\Files\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class SelectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function getFirst(): ?int
    {
        return (int)$this->post('file');
    }
}
