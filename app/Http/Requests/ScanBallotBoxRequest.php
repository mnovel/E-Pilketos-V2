<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanBallotBoxRequest extends FormRequest
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
            'barcode_data' => 'required|string',
            'participant' => 'required|exists:participants,id',
        ];
    }

    /**
     * Manipulate the validated data before returning
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (isset($data['participant'])) {
            $data['participant_id'] = $data['participant'];
            unset($data['participant']);
        }

        return $data;
    }
}
