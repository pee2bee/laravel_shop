@extends('layouts.app')

@section('title','我的订单')

@section('content')

  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card">
        <div class="card-header"><h2>我的订单</h2></div>
        <div class="card-body">
          <ul class="list-group-item">
            @foreach($orders as $order)
              <li>
                <div class="card panel">
                  <div class="card-header">
                    订单号：{{ $order->no }}
                    <span class="float-right">{{ $order->created_at->diffForHumans() }}</span>
                  </div>
                  <div class="card-body">
                    <table class="table">
                      <thead>
                      <tr>

                        <th class="text-center">商品信息</th>
                        <th class="text-center">单价</th>
                        <th class="text-center">数量</th>
                        <th class="text-center">订单总价</th>
                        <th class="text-center">状态</th>
                        <th class="text-center">操作</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach($order->items as $key => $item)
                        <tr>
                          <td class="product-info">
                            <div class="preview">
                              <a href="{{ route('products.show',[$item->product_id]) }}" target="_blank">
                                <img src="{{ asset("storage/".$item->product->image) }}">
                              </a>
                            </div>
                            <div>
                            <span class="product-title">
                              <a
                                href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                            </span>
                              <span class="sku-title">{{ $item->productSku->title }}</span>
                            </div>
                          </td>
                          <td class="sku-price text-center">￥{{ $item->price }}</td>
                          <td class="sku-amount text-center">{{ $item->amount }}</td>
                          @if($key === 0)
                            <td rowspan="{{ count($order->items) }}" class="text-center total-amount">
                              ￥{{ $order->total_amount }}</td>
                            <td rowspan="{{ count($order->items) }} " class="text-center">
                              @if($order->paid_at)
                                @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                                  已支付
                                @else
                                  {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                                @endif
                              @elseif($order->closed)
                                已关闭
                              @else
                                未支付 <br>
                                请于 {{ $order->created_at->addSeconds(config('app.order_ttl'))->format('d号 H:i') }}前完成支付
                                <br>
                                否则订单自动关闭
                              @endif
                            </td>
                            <td rowspan="{{ count($order->items) }}" class="text-center">
                              <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm">查看订单</a>
                              <!-- 评价入口开始 -->
                              @if($order->paid_at)
                                <a class="btn btn-success btn-sm"
                                   href="{{ route('orders.review.show', ['order' => $order->id]) }}">
                                  {{ $order->reviewed ? '查看评价' : '评价商品' }}
                                </a>
                            @endif
                            <!-- 评价入口结束 -->
                            </td>
                          @endif
                        </tr>
                      @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </li>
              <br>
            @endforeach
          </ul>
          <div class="float-right">{{ $orders->render() }}</div>
        </div>
      </div>
    </div>
  </div>

@stop

@section('js')

@stop
