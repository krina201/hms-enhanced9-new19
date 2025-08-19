<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class BatchLabelsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('labels.print') ?? false;
    }
    public function rules(): array
    {
        return [
            'ids' => ['required','array','max:200'],
            'ids.*' => ['integer','min:1'],
        ];
    }
}
