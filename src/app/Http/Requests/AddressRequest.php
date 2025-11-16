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
            // 郵便番号：必須、ハイフンあり8文字固定（例：123-4567）
            'shipping_postal_code' => [
                'required',
                'regex:/^\d{3}-\d{4}$/', // 3桁-4桁形式のみOK
            ],

            // 住所：必須
            'shipping_address_line' => [
                'required',
                'string',
                'max:255',
            ],

            // 建物名：任意
            'shipping_building' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

        public function messages(): array
    {
        return[
            'shipping_postal_code.required' => '郵便番号を入力してください。',
            'shipping_postal_code.regex' => '郵便番号はハイフン込みの8文字(例：123-4567)入力してください。',
            'shipping_address_line.required' => '住所を入力してください。',
        ];
    }
}
