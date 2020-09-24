@extends('layouts.app')

@section('title','收藏列表')

@section('content')
  <div class="row">
    <div class="col-lg-10 offset-lg-1">
      <div class="card">
        <div class="card-header">我的收藏 - 共{{ $total }}条</div>
        <div class="card-body">
          <!-- 筛选组件开始 -->
          <form action="{{ route('products.favorites') }}" class="search-form">
            <div class="form-row">
              <div class="col-md-9">
                <div class="form-row">
                  <div class="col-auto"><input type="text" class="form-control form-control-sm" name="search"
                                               placeholder="搜索"></div>
                  <div class="col-auto">
                    <button class="btn btn-primary btn-sm">搜索</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
          <!-- 筛选组件结束 -->

          <div class="row products-list">
            @foreach($products as $product)

              <div class="col-3 product-item">
                <div class="product-content">
                  <a href="{{ route('products.show',['product'=>$product->id]) }}">
                    <div class="top">
                      <div class="img"><img src="{{ asset("storage/$product->image") }}" alt=""></div>
                      <div class="price"><b>￥</b>{{ $product->price }}</div>
                      <div class="title">{{ $product->title }}</div>
                    </div>
                  </a>
                  <div class="bottom">
                    <div class="sold_count">销量 <span>{{ $product->sold_count }}笔</span></div>
                    <div class="review_count">浏览 <span>{{ $product->view_count }}</span></div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="float-right">{{ $products->appends($filters)->render() }}</div>  <!-- 只需要添加这一行 -->
        </div>
      </div>
    </div>

  </div>
@stop

@section('js')
  <script>
      var filters = {!! json_encode($filters) !!}
      $(document).ready(function () {
          //保留用户搜索和排序条件
          $('.search-form input[name=search]').val(filters.search)

          //按钮点击提交表单
          $('.search-form button').click(function () {
              $('.search-form').submit()
          })
      })
  </script>
@stop
