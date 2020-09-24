<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseOrder implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Order $order, $delay ) {
        //
        $this->order = $order;
        //设置延时时间
        $this->delay( $delay );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //已支付
        if ( $this->order->paid_at ) {
            return;
        }

        //未支付
        //通过事务执行sql
        \DB::transaction( function () {
            //关闭订单
            $this->order->update( [ 'closed' => true ] );
            foreach ( $this->order->items as $item ) {
                //把每一项的库存加回去
                $item->productSku->addStock( $item->amount );
            }
        } );
    }
}
