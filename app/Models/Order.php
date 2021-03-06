<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $no
 * @property int $user_id
 * @property array $address
 * @property string $total_amount
 * @property string|null $remark
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $payment_method
 * @property string|null $payment_no
 * @property string $refund_status
 * @property string|null $refund_no
 * @property bool $closed
 * @property bool $reviewed
 * @property string $ship_status
 * @property array|null $ship_data
 * @property array|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereClosed( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereExtra( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNo( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaidAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentMethod( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentNo( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundNo( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundStatus( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRemark( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereReviewed( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipData( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipStatus( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalAmount( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId( $value )
 * @mixin \Eloquent
 */
class Order extends Model {
    //
    use DefaultDatetimeFormat;//格式化时间 trait
    //定义常量，退款状态
    const REFUND_STATUS_PENDING = 'pending';//未决的，未申请退款
    const REFUND_STATUS_APPLIED = 'applied';//已应用，申请了退款
    const REFUND_STATUS_PROCESSING = 'processing';//退款中
    const REFUND_STATUS_SUCCESS = 'success';//退款成功
    const REFUND_STATUS_FAILED = 'failed';//退款失败
    //定义常量，物流状态
    const SHIP_STATUS_PENDING = 'pending';//未决的，未发物流
    const SHIP_STATUS_DELIVERED = 'delivered';//邮递中
    const SHIP_STATUS_RECEIVED = 'received';//已接收，已签收
    //常量映射对应的值
    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    //可批量填充的字段
    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    //指定字段的类型
    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    //指定时间类型，会格式化成Carbo对象，像created_at 和 updated_at 一样
    protected $dates = [
        'paid_at',
    ];

    //重写模型的boot方法
    protected static function boot() {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating( function ( $model ) {
            // 如果模型的 no 字段为空
            if ( ! $model->no ) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if ( ! $model->no ) {
                    return false;
                }
            }
        } );
    }

    //关联模型user
    public function user() {
        return $this->belongsTo( User::class );
    }

    //关联模型orderItem
    public function items() {
        return $this->hasMany( OrderItem::class );
    }

    //创建订单号，如果订单号已存在就记录错误终止创建订单
    public static function findAvailableNo() {
        // 订单流水号前缀
        $prefix = date( 'YmdHis' );
        for ( $i = 0; $i < 10; $i ++ ) {
            // 随机生成 6 位的数字
            $no = $prefix . str_pad( random_int( 0, 999999 ), 6, '0', STR_PAD_LEFT );
            // 判断是否已经存在
            if ( ! static::query()->where( 'no', $no )->exists() ) {
                return $no;
            }
        }
        \Log::warning( 'find order no failed' );

        return false;
    }

    //创建退款单号
    public static function createRefundNo() {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
            //如果有重复的就继续生成
        } while ( self::query()->where( 'refund_no', $no )->first() );

        return $no;
    }

    //关联优惠券
    public function coupon() {
        return $this->belongsTo( Coupon::class, 'coupon_code_id' );
    }
}
