<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'shipping_postcode' => 'required | regex:/^\d{3}-\d{4}$/',
            'shipping_address' => 'required',
            'shipping_building' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'shipping_postcode.required' => '郵便番号を入力してください',
            'shipping_postcode.regex' => '郵便番号は「123-4567」の形式で入力してください',
            'shipping_address.required' => '住所を入力してください',
        ];
    }

}
