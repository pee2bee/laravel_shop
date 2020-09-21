<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DateTimeInterface;
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use \Illuminate\Auth\MustVerifyEmail;
    // 加上这个 Trait 下面那个序列化时间就可以省略了
    use DefaultDatetimeFormat;
   /* /**
 * laravel7保存时间默认是UTC时间 带TZ，直接保存到数据库timestamp格式会报错
 * 报错信息SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '2020-09-15T08:06:57.000000Z' for column 'created_at'
 * 需要在模型中重写序列化时间的方法
 * @param DateTimeInterface $date
 *
 * @return string

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','created_at','updated_at','email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function addresses(  ) {
        return $this->hasMany(Address::class,'user_id');
    }


}
