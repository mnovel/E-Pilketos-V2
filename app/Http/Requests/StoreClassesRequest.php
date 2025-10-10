<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassesRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:classes,name',
            'max_users' => 'required|integer|min:1|max:70',
            'election_session' => 'required|uuid|exists:election_sessions,id',
        ];
    }

    /**
     * Manipulate the validated data before returning
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (isset($data['election_session'])) {
            $data['election_session_id'] = $data['election_session'];
            unset($data['election_session']);
        }

        return $data;
    }
}
