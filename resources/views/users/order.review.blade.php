@extends('layouts.app')

@section('title','订单评价')

@section('content')
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card">
        <div class="card-header"><h2>订单评价</h2></div>
        <div class="card-body">
          <table>
            <tbody>
            <tr>
              <td>商品名称</td>
              <td>打分</td>
              <td>评价</td>
            </tr>
            @foreach($order->items as $key => $item)
              <tr>
                <td class="product-info">
                  <div class="preview">
                    <a href="{{ route('orders.show',['order'=>$item->product_id]) }}" target="_blank">
                      <img src="{{ asset('storage/'.$item->product->image) }}" alt="商品图">
                    </a>
                  </div>
                  <div>
                    <span class="product-title">
                      <a href="{{ route('orders.show',['order'=>$item->product_id]) }}"
                         target="_blank">{{ $item->product->title }}</a>
                    </span>
                    <span class="sku-title">
                      {{ $item->productSku->title }}
                    </span>
                  </div>
                  <input type="hidden" name="reviews[{{$key}}][id]" value="{{ $item->id }}">
                </td>
                <td class="vertical-middle">
                  {{--如果订单已经评价，展示评分--}}
                  @if($order->reviewed)
                    <span class="rating-star-yew">{{ str_repeat('★',$item->rating) }}</span><span class="reting-star-no">{{ str_repeat('☆') }}</span>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

@stop
