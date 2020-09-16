<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends Request
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
            //
            'province_name'      => 'required',
            'city_name'          => 'required',
            'district_name'      => 'required',
            'strict'       => 'required',
            'contact_name'  => 'required',
            'contact_phone' => 'required',
        ];
    }

    public function messages(  ) {
        return [
            'province.required' => '选择省',
            'city.required' => '选择市',
            'district.required' => '选择区',
            'strict.required' => '填写详细地址',
            'contact_name.required' => '填写联系人',
            'contact_phone.required' => '填写手机号'
        ];
    }
}
