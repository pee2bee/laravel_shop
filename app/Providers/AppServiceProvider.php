<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //单例模式，向服务容器注入一个支付宝 alipay 对象
        $this->app->singleton( 'alipay', function () {
            $config = config( 'pay.alipay' ); //读取配置信息
            //判断当前环境，如果不是生产环境就设成开发模式
            if ( app()->environment() !== 'production' ) {
                $config['mode']         = 'dev';
                $config['notify_url']   = 'http://requestbin.net/r/1ez2hsr1';
                $config['return_url']   = route( 'payment.alipay.return' );
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }

            //创建一个支付宝支付对象
            return Pay::alipay( $config );
        } );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        //
        if ( config( 'app.debug' ) ) {
            $this->app->register( 'VIACreative\SudoSu\ServiceProvider' );
        }
    }
}
