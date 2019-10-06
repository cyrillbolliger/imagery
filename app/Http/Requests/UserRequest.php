<?php

namespace App\Http\Requests;

use App\Rules\UserLogoRule;
use App\Rules\UserManagedByRule;
use App\Rules\PasswordRule;
use App\Rules\SuperAdminRule;
use App\Rules\UniqueUpdateRule;
use Illuminate\Foundation\Http\FormRequest;
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
            'email'        => ['required', 'max:170', 'email', new UniqueUpdateRule($this->user, 'users')],
            'password'     => ['sometimes', new PasswordRule()],
            'managed_by'   => ['required', 'exists:groups,id', new UserManagedByRule($this->user)],
            'default_logo' => ['nullable', 'exists:logos,id', new UserLogoRule($this->user)],
            'super_admin'  => ['required', 'boolean', new SuperAdminRule($this->user)],
            'lang'         => ['required', Rule::in(\App\User::LANGUAGES)]
        ];
    }
}
