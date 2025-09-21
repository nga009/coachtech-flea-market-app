<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required | string | max:20',
            'email' => 'required | email',
            'password' => 'required | string | min:8',
            'password_confirmation' => 'required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // パスワードvs確認用パスワード不一致の際、確認用パスワードにエラーメッセージが紐づくよう独自チェックとする
            if ($this->password !== $this->password_confirmation) {
                $validator->errors()->add('password_confirmation', 'パスワードと一致しません');
            }
        });
    }
}
