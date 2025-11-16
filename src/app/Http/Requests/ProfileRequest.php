<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image'=>'nullable|mimes:jpeg,png',
            'name'=>'required|max:20',
            'default_postal_code'=>'required|regex:/^\d{3}-\d{4}$/',
            'default_address_line'=>'required',
            'default_building'=>'nullable'
        ];
    }

    public function messages(): array
    {
        return[
            'profile_image.mimes'=>'画像は「.png」または「.jpeg」形式でアップロードしてください',
            'name.required'=>'お名前を入力してください',
            'name.max'=>'お名前は20文字以内で入力してください',
            'default_postal_code.required'=>'郵便番号を入力してください',
            'default_postal_code.regex'=>'郵便番号はハイフン込みの8文字(例：123-4567)で入力してください',
            'default_address_line.required'=>'住所を入力してください',
        ];
    }
}
