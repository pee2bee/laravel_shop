@extends('layouts.app')

@section('title','订单评价')

@section('content')
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card">
        <div class="card-header"><h2>订单评价</h2></div>
        <div class="card-body">
          <form action="{{ route('orders.review.store',[$order->id]) }}" method="POST">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                      <span class="rating-star-yew">{{ str_repeat('★',$item->rating) }}</span><span
                        class="rating-star-no">{{ str_repeat('☆', 5-$item->rating) }}</span>
                    @else
                      <ul class="rate-area">
                        <input type="radio" id="5-star-{{ $key }}" name="reviews[{{ $key }}][rating]" value="5" checked>
                        <label for="5-star-{{ $key }}"></label>
                        <input type="raido" id="4-star-{{ $key }}" name="reviews[{{ $key }}][rating]" value="4" checked>
                        <label for="4-star-{{ $key }}"></label>
                        <input type="radio" id="3-star-{{ $key }}" name="reviews[{{ $key }}][rating]" value="3" checked>
                        <label for="3-star-{{ $key }}"></label>
                        <input type="radio" id="2-star-{{ $key }}" name="reviews[{{ $key }}][rating]" value="2" checked>
                        <label for="2-star-{{ $key }}"></label>
                        <input type="radio" id="1-star-{{ $key }}" name="reviews[{{ $key }}][rating]" value="1" checked>
                        <label for="1-star-{{ $key }}"></label>
                      </ul>
                    @endif
                  </td>
                  <td>
                    @if($order->reviewed)
                      {{ $item->review }}
                    @else
                      <textarea class="form-control {{ $errors->has('reviews.'.$key.'.review')? 'is-invalid' : '' }}"
                                name="reviews[{{ $key }}][review]" id="" cols="30" rows="10"></textarea>
                      @if($error->has('reviews.'.$key.'.review'))
                        @foreach($errors->get('reviews.'.$key.'.review') as $msg)
                          <span class="invalid-feedback" role="alert"></span>
                        @endforeach
                      @endif
                    @endif
                  </td>
                </tr>
              @endforeach
              </tbody>
              <tfooter>
                <tr>
                  <td colspan="3" class="text-center">
                    @if($order->reviewed)
                      <button type="submit" class="btn btn-primary center-block">提交</button>
                    @else
                      <a href="{{ route('orders.show',[$order->id]) }}">
                        <button class="btn btn-primary">查看订单</button>
                      </a>
                    @endif
                  </td>
                </tr>
              </tfooter>
            </table>
          </form>
        </div>
      </div>
    </div>
  </div>

@stop
