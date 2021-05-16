<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'username'  => 'required|min:4|max:14|unique:users,username,'.$this->get('id').',id',
        ];
        if ($this->get('password') || $this->get('password_confirmation')){
            $rules['password'] = 'required|confirmed|min:6|max:14';
        }
        return $rules;
    }
}
