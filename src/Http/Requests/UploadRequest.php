<?php

namespace Khamsolt\Orchid\Files\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attachment.source'        => ['required', 'file', 'max:' . config('orchid-files.size')],
            'attachment.user_id'       => ['required', 'numeric', 'exists:users,id'],
            'attachment.original_name' => ['nullable', 'string', 'max:255'],
            'attachment.sort'          => ['numeric', 'nullable'],
            'attachment.description'   => ['string', 'nullable'],
            'attachment.alt'           => ['string', 'nullable'],
            'attachment.group'         => ['string', 'nullable'],
        ];
    }
}
