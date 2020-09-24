@extends('layouts.app')

@section('title', '购物车')

@section('content')
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card">
        <div class="card-header"><h2>购物车</h2></div>
        <div class="card-body">
          <table class="table table-striped">
            <thead>
            <tr>
              <th><input type="checkbox" id="select-all"></th>
              <th>商品信息</th>
              <th>单价</th>
              <th>数量</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody class="product_list">
            @foreach($cartItems as $item)
              <tr data-id="{{ $item->product_sku_id }}">
                <td>
                  <input type="checkbox" name="select"
                         value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
                </td>
                <td class="product_info">
                  <div class="preview">
                    <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                      <img src="{{ $item->productSku->product->image_url }}">
                    </a>
                  </div>
                  <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
              <span class="product_title">
                <a target="_blank"
                   href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
              </span>
                    <span class="sku_title">{{ $item->productSku->title }}</span>
                    @if(!$item->productSku->product->on_sale)
                      <span class="warning">该商品已下架</span>
                    @endif
                  </div>
                </td>
                <td><span class="price">￥{{ $item->productSku->price }}</span></td>
                <td>
                  <input type="text" class="form-control form-control-sm amount"
                         @if(!$item->productSku->product->on_sale) disabled @endif name="amount"
                         value="{{ $item->amount }}">
                </td>
                <td>
                  <button class="btn btn-sm btn-danger btn-remove">移除</button>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>

          <form class="form-horizontal" role="form" id="order-form">
            <div class="form-group row">
              <label class="col-form-label col-sm-3 text-md-right">选择收货地址</label>
              <div class="col-sm-9 col-md-7">
                <select class="form-control" name="address">
                  @foreach($addresses as $address)
                    <option
                      value="{{ $address->id }}">{{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3 text-md-right">备注</label>
              <div class="col-sm-9 col-md-7">
                <textarea name="remark" class="form-control" rows="3"></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="offset-sm-3 col-sm-3">
                <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

@stop

@section('js')
  <script>
      $(document).ready(function () {

          //监听移除按钮
          $('.btn-remove').click(function () {
              var id = $(this).closest('tr').data('id')
              swal({
                  title: "确认移除？",
                  icon: 'warning',
                  buttons: ['取消', '确定'],
                  dangerMode: true
              })
                  .then(function (willDo) {
                      //点取消，willDo 值为false
                      if (!willDo) {
                          return;
                      }
                      axios.delete('cartItems/' + id)
                          .then(function () {
                              location.reload()
                          })
                  })
          })

          // 监听 全选/取消全选 单选框的变更事件
          $('#select-all').change(function () {
              // 获取单选框的选中状态
              // prop() 方法可以知道标签中是否包含某个属性，当单选框被勾选时，对应的标签就会新增一个 checked 的属性
              var checked = $(this).prop('checked');
              // 获取所有 name=select 并且不带有 disabled 属性的勾选框
              // 对于已经下架的商品我们不希望对应的勾选框会被选中，因此我们需要加上 :not([disabled]) 这个条件
              $('input[name=select][type=checkbox]:not([disabled])').each(function () {
                  // 将其勾选状态设为与目标单选框一致
                  $(this).prop('checked', checked);
              });
          });

          //监听创建订单按钮
          $('.btn-create-order').click(function () {
              //构建请求参数，将用户地址和备注内容写入参数
              var data = {
                  address_id: $('form select[name=address]').val(),
                  items: [],
                  remark: $('textarea[name=remark]').val(),
              }
              //遍历table中的所有tr data-id,取到sku id
              $('table tr[data-id]').each(function () {
                  //获取当前行的单选框
                  var checkbox = $(this).find('input[type=checkbox][name=select]');
                  //如果单选框没有被选中或者被禁用，就直接返回
                  if (checkbox.prop('disable') || !checkbox.prop('checked')) {
                      return;
                  }
                  //获取当前输入数量
                  var input = $(this).find('input[name=amount]');
                  //如果数量为0 或者不是数字，也返回
                  //isNan() is not a number
                  if (input.val() === 0 || isNaN(input.val())) {
                      return;
                  }
                  //把sku的数量和id存入到请求数据中
                  data.items.push({
                      id: $(this).data('id'),
                      amount: input.val(),
                  })
              })

              //发送请求
              axios.post('{{ route('orders.store') }}', data)
                  .then(function (response) {
                      swal('订单提交成功', '', 'success')
                  }, function (error) {
                      if (error.response.status === 422) {
                          // http 状态码为 422 代表用户输入校验失败
                          var html = '<div>';
                          _.each(error.response.data.errors, function (errors) {
                              _.each(errors, function (error) {
                                  html += error + '<br>';
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
      })
  </script>
@stop
