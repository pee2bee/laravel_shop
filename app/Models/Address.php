<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Address
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int|null $province_code 省id
 * @property string|null $province_name 省
 * @property int|null $city_code 市id
 * @property string|null $city_name 市
 * @property int|null $district_code 区id
 * @property string|null $district_name 区
 * @property string|null $strict 街道
 * @property string|null $zipcode 邮编
 * @property string $contact_name 收件人
 * @property string $contact_phone 手机
 * @property string|null $last_used_at 最后使用时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_address
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCityCode( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCityName( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereContactName( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereContactPhone( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereDistrictCode( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereDistrictName( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLastUsedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereProvinceCode( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereProvinceName( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereStrict( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUserId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereZipcode( $value )
 * @mixin \Eloquent
 */
class Address extends Model {
    /**
     * laravel7保存时间默认是UTC时间 带TZ，直接保存到数据库timestamp格式会报错
     * 报错信息SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '2020-09-15T08:06:57.000000Z' for column 'created_at'
     * 需要在模型中重写序列化时间的方法
     *
     * @param DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate( DateTimeInterface $date ) {
        return $date->format( 'Y-m-d H:i:s' );
    }

    //

    public function user() {
        return $this->belongsTo( User::class );
    }

    //访问器，按照驼峰式命名规则，访问$this->full_address时返回
    public function getFullAddressAttribute() {
        return $this->province_name . ' ' . $this->city_name . ' ' . $this->district_name . ' ' . $this->strict;
    }

    protected $fillable = [
        'province_name',
        'city_name',
        'district_name',
        'province_code',
        'city_code',
        'district_code',
        'strict',
        'contact_name',
        'contact_phone'
    ];


}
