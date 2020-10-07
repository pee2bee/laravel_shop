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
              {{--物流信息，已支付的展示物流信息--}}
              @if($order->paid_at)

                <div class="line">
                  <div class="line-label">
                    物流状态：
                  </div>
                  <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
                </div>
                {{--如果已发货或已收货，显示物流信息--}}
                @if($order->ship_status != \App\Models\Order::SHIP_STATUS_PENDING)
                  <div class="line">
                    <div class="line-label">
                      物流公司：
                    </div>
                    <div class="line-value">{{ $order->ship_data['express_company'] ?: '' }}</div>
                  </div>
                  <div class="line">
                    <div class="line-label">
                      物流单号：
                    </div>
                    <div class="line-value">{{ $order->ship_data['express_no'] ?: '' }}</div>
                  </div>
                @endif
                {{--退款信息--}}
                @if($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
                  <div class="line">
                    <div class="line-label">退款状态：</div>
                    <div class="line-value">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</div>
                  </div>
                  <div class="line">
                    <div class="line-label">退款理由：</div>
                    <div class="line-value">{{ $order->extra['refund_reason'] }}</div>
                  </div>
                @endif
              @endif
            </div>
            <div class="order-summary text-right">
              <!-- 展示优惠信息开始 -->
              @if($order->coupon)
                <div class="text-primary">
                  <span>优惠信息：</span>
                  <div class="value">{{ $order->coupon->description }}</div>
                </div>
              @endif
            <!-- 展示优惠信息结束 -->
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
                      {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
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
              {{----}}
              @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
                <div class="receive-button">
                  <form method="post" action="{{ route('orders.received', [$order->id]) }}">
                    <!-- csrf token 不能忘 -->
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-sm btn-success">确认收货</button>
                  </form>
                </div>
              @endif

              {{--已支付，显示申请退款按钮--}}
              @if($order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                <div class="refund-button">
                  <button class="btn btn-sm btn-danger" id="btn-apply-refund">申请退款</button>
                </div>
              @endif
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
@stop

@section('js')
  <script>
      $(document).ready(function () {

          //确认收货按钮提交
          $('.receive-button button[type=submit]').click(function () {
              swal({
                  title: '你确定要收货吗？',
                  icon: 'warning',
                  dangerMode: true,
                  buttons: ['取消', '确认收货']
              })
                  .then(function (willDo) {
                      if (!willDo) {
                          return
                      }
                      //发送请求
                      axios.post('{{ route('orders.received', [$order->id]) }}')
                          .then(function () {
                              //刷新页面
                              location.reload()
                          }, function (error) {
                              if (error.response.status === 401) {
                                  swal('请登录后再操作', '', 'error')
                              } else if (error.response && (error.response.msg || error.response.messga)) {
                                  //其他错误信息
                                  swal(error.response.msg ? error.response.msg : error.response.message, '', 'error')
                              } else {
                                  //系统挂了
                                  swal('系统错误', '', 'error')
                              }
                          })
                  })
          })

          //退款申请按钮提交
          $('#btn-apply-refund').click(function () {
              swal({
                  text: '请输入退款理由',
                  content: "input",
              }).then(function (input) {
                  // 当用户点击 swal 弹出框上的按钮时触发这个函数
                  if (!input) {
                      swal('退款理由不可空', '', 'error');
                      return;
                  }
                  // 请求退款接口
                  axios.post('{{ route('orders.refund.apply', [$order->id]) }}', {reason: input})
                      .then(function () {
                          swal('申请退款成功', '', 'success').then(function () {
                              // 用户点击弹框上按钮时重新加载页面
                              location.reload();
                          });
                      });
              });
          });
      })
  </script>
@stop
