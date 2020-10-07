<?php

namespace App\Models;

use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Coupon extends Model {
    //使用格式时间的trait,不然会带有时区
    use DefaultDatetimeFormat;
    //使用常量的方式来定义支持的优惠券类型
    const TYPE_FIXED = 'fixed';//固定的
    const TYPE_PERCENT = 'percent';//百分比

    public static $typeMap = [
        self::TYPE_FIXED   => '固定金额',
        self::TYPE_PERCENT => '百分比'
    ];

    protected $fillable = [
        'name',
        'type',
        'code',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled'
    ];

    //将数据库的值进行类型转换
    //使用eloquent取出时会进行转换
    protected $casts = [
        'enabled' => 'boolean'
    ];

    //指明字段是日期类型，会格式化成Carbon实例，跟created_at一样
    protected $dates = [
        'not_before',
        'not_after'
    ];

    //生成优惠码
    public static function createCouponCode( $length = 8 ) {
        do {
            //生成一个指定长度的随机字符串，并转成大写
            $code = strtoupper( Str::random( $length ) );
            //如果已经存在就继续循环生成新的
        } while ( self::query()->where( 'code', $code )->exists() );

        return $code;
    }

    protected $appends = [ 'description' ];

    //重新拼接优惠券描述，使用访问器
    public function getDescriptionAttribute() {
        $str = '';//保存拼接后的字符串
        //对最低金额有要求
        if ( $this->min_amount > 0 ) {
            $str = '满';

        }
        //根据类型判断使用什么词拼接，满减固定金额
        if ( $this->type === self::TYPE_FIXED ) {
            $str .= str_replace( '.00', '', ( $this->min_amount . '元减' . $this->value . '元' ) );
        } elseif ( $this->type === self::TYPE_PERCENT ) {
            $str .= str_replace( '.00', '', ( $this->min_amount . '元优惠' . $this->value . '%' ) );
        }

        return $str;
    }

    //检查优惠券是否可用
    public static function checkCodeValid( $code, Order $order = null, User $user = null ) {

        //数据库中查询是否存在
        if ( ! $coupon = self::query()->where( 'code', $code )->first() ) {
            //不存在
            throw new InvalidRequestException( '优惠券不存在', 404 );
        }

        //存在，判断是否启用
        if ( ! $coupon->enabled ) {
            throw new InvalidRequestException( '优惠券未启用', 403 );
        }

        //判断次数是否用完
        if ( $coupon->used >= $coupon->total ) {
            //已用完次数
            throw new InvalidRequestException( '该优惠券可用次数已为0', 403 );
        }

        //已启用，判断是否在生效时间内
        /*$date = new Carbon();
        $date->gt()*/
        //gt() greater 更大，更晚，返回true or false
        if ( $coupon->not_before && $coupon->not_before->gt( Carbon::now() ) ) {
            //未到生效时间
            throw new InvalidRequestException( '优惠券还没到生效时间', 403 );
        }

        if ( $coupon->not_after && $coupon->not_after->isBefore( Carbon::now() ) ) {
            //已经过了有效时间
            throw new InvalidRequestException( '优惠券已过有效期', 403 );
        }

        //如果需要检查订单
        if ( $order ) {
            //检查订单是否满足的优惠券满减条件
            if ( $coupon->min_amount > $order->total_amount ) {
                throw new InvalidRequestException( '该订单未满足满减条件，不能使用', 403 );
            }
        }

        //如果需要检查用户，一个用户只能使用该优惠券一次
        if ( $user ) {
            //查找该用户是否有订单已经使用过该优惠券，(订单是已经支付，且未退款成功的(退款成功，优惠券会返还)) 或者 （订单未支付，且未关闭，等着给钱的）
            /* $sql = "select * from orders where user_id = $user->id and coupon_code_id = $coupon->id
                     and ( (paid_at is not null and refund_status != 'success') or (paid_at is null and closed = 0))";*/
            //使用eloquent就是
            $used = Order::query()->where( 'user_id', $user->id )->where( 'coupon_code_id', $coupon->id )
                         ->where( function ( $query ) {
                             $query->where( function ( $query ) {
                                 $query->whereNotNull( 'paid_at' )->where( 'refund_status', '!=', Order::REFUND_STATUS_SUCCESS );
                             } )->orWhere( function ( $query ) {
                                 $query->whereNull( 'paid_at' )->where( 'closed', '0' );
                             } );
                         } )->exists();
            //toSql() 可以输入sql语句
            //然后在浏览器Chrome 浏览器打开控制台，点开 Network–>XHR标签下看到 SQL 语句

            //如果已经使用过了
            if ( $used ) {
                throw new InvalidRequestException( '该优惠券已经使用过了' );
            }
        }

        return $coupon;
    }

    //传入总金额，返回优惠计算后的总金额
    public function countTotalAmount( $withoutCouponAmount ) {

        $totalAmount = '';
        //减固定金额
        if ( $this->type === self::TYPE_FIXED ) {
            $totalAmount = $withoutCouponAmount - $this->value;
        } elseif ( $this->type === self::TYPE_PERCENT ) {
            //减百分比
            $totalAmount = ( 1 - 0.01 * $this->value ) * $withoutCouponAmount;
        }

        return $totalAmount;
    }

    //改变已使用次数，下单，关闭订单会改变次数，通过传参true or false 来决定是加还是减
    public function changeUsed( $increment = true ) {
        if ( $increment ) {
            //检查实例id 和次数对不对，然后自增
            return $this->query()->where( 'id', $this->id )->where( 'used', '<', 'total' )->increment( 'used' );
        } else {
            return $this->decrement( 'used' );
        }
    }
}
