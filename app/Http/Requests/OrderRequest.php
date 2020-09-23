<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
            'address_id' =>[
                'required',
                //必须是请求用户对应的地址id
                Rule::exists('addresses','id')->where('user_id',$this->user()->id)],
            'items' => ['required', 'array'],
            'items.*.id' =>[//检查items数组下的每一个数组的id
                'required',
                function($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value) ) {
                        return $fail('商品不存在');
                    }
                    if (!$sku->product->on_sale){
                        return $fail('商品已下架');
                    }
                    if ($sku->stock === 0){
                        return $fail('商品已售完');
                    }
                }
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],//检查items下的每一个数组的amount
        ];
    }
}
