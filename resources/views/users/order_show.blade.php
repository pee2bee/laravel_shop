@extends('layouts.app')
@section('title', '订单详情')

@section('content')
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card">
        <div class="card-header"><h2>订单详情</h2></div>
        <div class="card-body">
          <table class="table">
            <thead>
            <tr>
              <th>商品信息</th>
              <th class="text-center">单价</th>
              <th class="text-center">数量</th>
              <th class="float-right item-amount">小计</th>
            </tr>
            </thead>
            @foreach($order->items as $key => $item)
              <tr>
                <td class="product-info">
                  <div class="preview">
                    <a target="_blank" href="{{ route('products.show',$item->product_id) }}">
                      <img src="{{ asset('storage/'.$item->product->image) }}" alt="商品封面">
                    </a>
                  </div>
                  <div>
                    <sapn class="product-title">
                      <a href="{{ route('products.show',$item->product_id) }}">{{ $item->product->title }}</a>
                    </sapn>
                    <span class="sku-title">{{ $item->productSku->title }}</span>
                  </div>
                </td>
                <td class="sku-price text-center vertical-middle">￥{{ $item->price }}</td>
                <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
                <td class="item-amount text-right vertical-middle">
                  ￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
              </tr>
            @endforeach
            <tr>
              <td colspan="4"></td>
            </tr>
          </table>

          <div class="order-bottom">
            <div class="order-info">
              <div class="line">
                <div class="line-label">
                  收货地址：
                </div>
                <div class="line-value">{{ join(' ',$order->address) }}</div>
              </div>
              <div class="line">
                <div class="line-label">
                  订单备注：
                </div>
                <div class="line-value">{{ $order->remark?: '-' }}</div>
              </div>
              <div class="line">
                <div class="line-label">
                  订单编号：
                </div>
                <div class="line-value">{{ $order->no }}</div>
              </div>
            </div>
            <div class="order-summary text-right">
              <div class="total-amount">
                <span>订单总价：</span>
                <div class="value">￥{{ $order->total_amount }}</div>
              </div>
              <div>
                <sqan>订单状态</sqan>
                <div class="value">
                  @if($order->paid_at)
                    @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                      已支付
                    @else
                      {{ \App\Models\Order::$refundStatusMap($order->refund_status) }}
                    @endif
                  @elseif($order->close)
                    已关闭
                  @else
                    未支付
                  @endif
                </div>
              </div>
              <!-- 支付按钮开始 -->
              @if(!$order->paid_at && !$order->closed)
                <div class="payment-buttons">
                  <a class="btn btn-primary btn-sm"
                     href="{{ route('payment.alipay', ['order' => $order->id]) }}">支付宝支付</a>
                </div>
              @endif
            <!-- 支付按钮结束 -->
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
@stop
