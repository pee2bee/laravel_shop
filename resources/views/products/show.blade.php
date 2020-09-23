@extends('layouts.app')

@section('title','商品详情')

@section('content')
  <div class="row">
    <div class="col-lg-10 offset-lg-1">
      <div class="card">
        <div class="card-body product-info">
          <div class="row">
            <div class="col-5">
              <img class="cover" src="{{ asset("storage/$product->image") }}" alt="">
            </div>
            <div class="col-7">
              <div class="title">{{ $product->title }}</div>
              <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
              <div class="sales_and_reviews">
                <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
                <div class="review_count">累计评价 <span class="count">{{ $product->view_count }}</span></div>
                <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
              </div>
              <div class="skus">
                <label>选择</label>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                  @foreach($product->productSkus as $sku)
                    <label
                      class="btn sku-btn"
                      data-price="{{ $sku->price }}"
                      data-stock="{{ $sku->stock }}"
                      data-toggle="tooltip"
                      title="{{ $sku->description }}"
                      data-placement="bottom">
                      <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title .$sku->id}}
                    </label>
                  @endforeach
                </div>
              </div>
              <div class="cart_amount"><label>数量</label><input type="text" name="amount" class="form-control form-control-sm" value="1"><span>件</span><span class="stock"></span></div>
              <div class="buttons">
                @if($favored)
                  <button class="btn btn-danger btn-disfavor">取消收藏</button>
                @else
                  <button class="btn btn-success btn-favor">❤ 收藏</button>
                @endif
                <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
              </div>
            </div>
          </div>
          <div class="product-detail">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">商品详情</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">用户评价</a>
              </li>
            </ul>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
                {!! $product->description !!}
              </div>
              <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
              </div>
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
          $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
          $('.sku-btn').click(function () {
              $('.product-info .price span').text($(this).data('price'));
              $('.product-info .stock').text('库存：' + $(this).data('stock') + '件');
          });

          //监听收藏按钮
          $('.product-info .btn-favor').click(function () {
              axios.post('{{ route('products.favor' ,['product'=>$product->id]) }}')
                  .then(function () { //成功执行
                  swal('操作成功', '','success');
                  location.reload()
              }, function (error) { //失败执行
                  //判断返回状态码，如果是401，就提示未登录
                  if (error.response && error.response.status === 401) {
                      swal('请先登录','','error');
                  }else if (error.response && (error.response.msg || error.response.messga)) {
                      //其他错误信息
                      swal(error.response.msg? error.response.msg : error.response.message,'','error')
                  }else {
                      //系统挂了
                      swal('系统错误','','error')
                  }
              })
          })
          //监听收藏按钮
          $('.product-info .btn-disfavor').click(function () {
              axios.delete('{{ route('products.disfavor' ,['product'=>$product->id]) }}')
                  .then(function () { //成功执行
                      swal('操作成功', '','success');
                      location.reload()
                  }, function (error) { //失败执行
                      //判断返回状态码，如果是401，就提示未登录
                      if (error.response && error.response.status === 401) {
                          swal('请先登录','','error');
                      }else if (error.response && (error.response.msg || error.response.messga)) {
                          //其他错误信息
                          swal(error.response.msg? error.response.msg : error.response.message,'','error')
                      }else {
                          //系统挂了
                          swal('系统错误','','error')
                      }
                  })
          })

          //监听加入购物车按钮
          $('.btn-add-to-cart').click(function () {
              if ($('label.active input[name=skus]').length <= 0 ){
                  swal('请选择要购买的商品','','error')
                  return;
              }
              axios.post('{{ route('cart.store') }}',{
                  product_sku_id: $('label.active input[name=skus]').val(),
                  amount: $('input[name=amount]').val()
              })
              .then(function () {
                  swal('加入购物车成功','','success')
                  location.href = '{{ route('cart.index') }}'
              },function (error) {
                  if (error.response.status === 401) {
                      swal('请先登录','','error')
                  }else if (error.response.status === 422) {

                      // http 状态码为 422 代表用户输入校验失败
                      var html = '<div>';
                      _.each(error.response.data.errors, function (errors) {
                          _.each(errors, function (error) {
                              html += error+'<br>';
                          })
                      });
                      html += '</div>';
                      swal({content: $(html)[0], icon: 'error'})
                  } else {

                      // 其他情况应该是系统挂了
                      swal('系统错误', '', 'error');
                  }
              })
          })
      });
  </script>
@stop
