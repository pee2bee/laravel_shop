<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

//校验评价输入
class SendReviewRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
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
            'reviews'          => [ 'required', 'array' ],
            'reviews.*.id'     => [
                'required',
                //判断每一项评价对应的id是否存在于order_items表中，且对应记录的order_id等于当前路由对应的order对象的id
                //路由orders/{order}，路由会根据order参数来实例化对应的模型对象
                //主要是判断存不存在订单项，该评论属不属于这个订单
                Rule::exists( 'order_items', 'id' )->where( 'order_id', $this->route( 'order' )->id )
            ],
            'reviews.*.rating' => [ 'required', 'integer', 'between:1,5' ],
            'reviews.review'   => 'required'
        ];
    }

    public function attributes() {
        return [
            'reviews.*.rating' => '评分',
            'reviews.*.review' => '评价'
        ];
    }
}
