<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    //
    public function payByAlipay( Order $order, Request $request ) {
        //判断订单是否属于当前用户
        $this->authorize( 'own', $order );//认证错误返回401错误
        //订单已支付或已关闭
        if ( $order->paid_at || $order->closed ) {
            throw new InvalidRequestException( '订单已支付或已关闭' );
        }

        //调用支付宝的网页支付
        return app( 'alipay' )->web( [
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject'      => '支付 大王 的订单：' . $order->no, // 订单标题
        ] );
    }

    //支付宝支付后前端返回，不涉及是否确实已支付的判断
    public function alipayReturn() {
        //校验提交的参数是否合法，返回检校后的参数
        try {
            $data = app( 'alipay' )->verify();
        } catch ( \Exception $e ) {
            $msg = '数据不正确';

            return view( 'pages.error', compact( 'msg' ) );
        }

        return view( 'pages.success', [ 'msg' => '付款成功' ] );
    }

    //支付宝服务端回调
    public function alipayNotify() {
        $data = app( 'alipay' )->verify();
        //打印到日记输出
        //\Log::debug( 'Alipay notify', $data->all() );
        //data数据：
        /*[2020-09-25 16:56:45] local.DEBUG: Alipay notify
         {
        "gmt_create":"2020-09-25 15:07:18",
        "charset":"utf-8","gmt_payment":"2020-09-25 15:07:27",
        "notify_time":"2020-09-25 15:07:28","subject":"支付 大王 的订单：20200925150518930213",
        "sign":"QEw4Hfh1MJksynU9Nvh2UdWlKYlfSKLnMdCx2tBHfLRHHE7lxE/yL+52Xg3dIwiZJyx5Z/8Yn9FSmh2kzlAm9CpxU1F38Rg4hN8Avxb/+1Dfb9KIhyvIZABDlyAqZ2AsvBPJ5nIPiwkxqtXvMCTHqUplJrTFH35rq2V8XahcFFaJNzzGsVtfYXhgeqrGsh/bxhgIQwSpNdkoUOCnVfKKEL/1hm6cxEJdX8pnbr8/zcKH45acu4v8EwH05c3J0lLgSrmfjj1xT4awsiW8UybSM99P9QH8ynLIams9kdephUe7PlZrKwdf2RWhVX0Bko84/JobtbpwdkjSD5RTikJvyw==",
        "buyer_id":"2088622954753683","invoice_amount":"158.00","version":"1.0",
        "notify_id":"2020092500222150727053680508797409",
        "fund_bill_list":"[{\"amount\":\"158.00\",\"fundChannel\":\"ALIPAYACCOUNT\"}]",
        "notify_type":"trade_status_sync","out_trade_no":"20200925150518930213",
        "total_amount":"158.00","trade_status":"TRADE_SUCCESS","trade_no":"2020092522001453680501260506",
        "auth_app_id":"2016102500755509","receipt_amount":"158.00","point_amount":"0.00","app_id":"2016102500755509",
        "buyer_pay_amount":"158.00","sign_type":"RSA2","seller_id":"2088102180967990"
        }*/

        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672

        //判断交易状态,如果交易关闭的TRADE_CLOSED，直接返回成功success字段给支付宝，这样支付宝就不会再发通知过来
        if ( ! in_array( $data->trade_status, [ 'TRADE_SUCCESS', 'TRADE_FINISHED' ] ) ) {
            return app( 'alipay' )->success();
        }

        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where( 'no', $data->out_trade_no )->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if ( ! $order ) {
            return 'fail';
        }

        $order->update( [
            'paid_at'        => Carbon::now(),
            'payment_method' => 'alipay',
            'payment_no'     => $data->trade_no //支付宝订单号
        ] );

        //广播已支付的事件
        $this->afterPaid( $order );

        //返回success字段给支付宝,就是返回 'success'字符串
        return app( 'alipay' )->success();
    }

    public function afterPaid( $order ) {
        event( new OrderPaid( $order ) );
    }
}
