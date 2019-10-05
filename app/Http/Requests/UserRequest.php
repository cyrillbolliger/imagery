<?php

namespace App\Http\Requests;

use App\Rules\PasswordRule;
use App\Rules\SuperAdminRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'   => 'required',
            'last_name'    => 'required',
            'email'        => 'required|email|unique:users,email,'.$this->user->id,
            'password'     => ['sometimes', new PasswordRule()],
            'managed_by'   => 'required|exists:users',
            'default_logo' => 'nullable|exists:logos',
            'super_admin'  => ['required', 'boolean', new SuperAdminRule($this->user)],
            'lang'         => ['required', Rule::in(\App\User::LANGUAGES)]
        ];
    }
}
