<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DisagreeRefundRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        //权限判断，先留空
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            //
            'reason' => 'required'
        ];
    }

    public function messages() {
        return [
            'reason.required' => '请填写拒绝原因'
        ];
    }
}
