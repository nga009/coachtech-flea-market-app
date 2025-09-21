<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class CommentRequest extends FormRequest
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
            'comment' => 'required | string | max:255',
        ];

    }

    public function messages()
    {
        return [
            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは255文字以内で入力してください',
        ];
    }

    /**
     * Ajax用のエラーレスポンス
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray(),
            ], 422);

            throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
        }

        parent::failedValidation($validator);
    }
}
