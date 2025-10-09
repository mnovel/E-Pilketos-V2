<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
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
            'user.name' => 'required|string',
            'user.email' => 'required|string|email|unique:users,email',
            'user.password' => 'required|string|min:6',
            'participant.nis' => 'required|digits:10|unique:participants,nis',
            'participant.class' => 'required|uuid|exists:classes,id',
        ];
    }

    /**
     * Manipulate the validated data before returning
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (isset($data['participant']['class'])) {
            $data['participant']['class_id'] = $data['participant']['class'];
            unset($data['participant']['class']);
        }

        return $data;
    }
}
