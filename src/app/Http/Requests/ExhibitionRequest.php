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
            'name' =>'required',
            'description' =>'required|max:255',
            'item_image' =>'required|mimes:jpeg,png',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id',
            'condition' =>'required',
            'price' =>'required|regex:/^[0-9]+$/|min:0',
        ];
    }
        public function messages(): array
    {
        return[
            'name.required'=>'商品名を入力してください',
            'description.required'=>'商品の説明を入力してください',
            'description.max'=>'商品説明は255文字以内で入力してください',
            'item_image.required' =>'商品の画像をアップロードしてください',
            'item_image.mimes' =>'画像は「.png」または「.jpeg」形式でアップロードしてください',
            'category_id.required' =>'カテゴリーを選んでください',
            'condition.required' =>'商品の状態を選択してください',
            'price.required' =>'商品の値段を入力してください',
            'price.regex' =>'値段は小数点なしの半角数字で入力してください',
            'price.min' =>'値段は０円以上で入力してください',
        ];
    }
}
