<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateElectionSessionsRequest extends FormRequest
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
            'name' => 'required|string|max:150|unique:election_sessions,name,' . $this->route('election_session')->id,
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date'   => 'required|date_format:Y-m-d H:i:s|after_or_equal:start_date',
            'status' => 'required|string|in:enable,disable',
        ];
    }
}
