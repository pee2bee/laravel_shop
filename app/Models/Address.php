<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * laravel7保存时间默认是UTC时间 带TZ，直接保存到数据库timestamp格式会报错
     * 报错信息SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '2020-09-15T08:06:57.000000Z' for column 'created_at'
     * 需要在模型中重写序列化时间的方法
     * @param DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    //
    public function user(  ) {
        return $this->belongsTo(User::class);
    }
    //访问器，按照驼峰式命名规则，访问$this->full_address时返回
    public function getFullAddressAttribute(  ) {
        return $this->province_name + ' ' + $this->city_name + ' ' + $this->district_name + ' ' + $this->strict;
    }
}
