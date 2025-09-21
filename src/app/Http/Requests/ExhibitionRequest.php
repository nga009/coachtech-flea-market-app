<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required | string | max:255',
            'item_image' => 'required | file | mimes:jpeg,png',
            'categories' => 'required',
            'condition' => 'required',
            'price' => 'required | integer | min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'item_image.required' => '商品画像を選択してください',
            'item_image.mimes' => '商品画像はJPEGまたはPNG形式でアップロードしてください',
            'categories.required' => '商品のカテゴリーを選択してください',
            'condition.required' => '商品の状態を入力してください',
            'price.required' => '商品価格を入力してください',
            'price.integer'  => '商品価格は数値で入力してください',
            'price.min'      => '商品価格は0円以上で入力してください',
        ];
    }

}
