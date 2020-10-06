<?php

namespace App\Models;

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
}
