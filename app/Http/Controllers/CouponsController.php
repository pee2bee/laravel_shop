<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Coupon;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponsController extends Controller {

    //查看优惠券详情，折扣，剩余时间等
    public function check( Request $request ) {
        $code = $request->code;

        //调用检查是否有效的方法，有效返回优惠券实例
        return Coupon::checkCodeValid( $code );
    }


}
