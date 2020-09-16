@extends('layouts.app')

@section('title','收货地址列表')

@section('content')
  <div class="row {{ route_class() }}">
    <div class="col-md-10 offset-md-1">
      <div class="card">
        <div class="card-header"><h2 class="text-center">收货地址列表</h2></div>
          <div class="card-body">
            <a class="btn btn-primary" href="{{ route('addresses.create') }}">添加地址</a>
            <table class="table">
              <thead>
              <tr>
                <th>收货人</th>
                <th>地址</th>
                <th>邮编</th>
                <th>电话</th>
                <th>操作</th>
              </tr>
              </thead>
              <tbody>
              @if(!count($addresses))
                <tr>
                    <td class="text-center" colspan="5">
                      <a class="btn btn-primary" href="{{ route('addresses.create')}}">
                        还没有收货地址请添加收货地址哦！
                      </a>
                    </td>
                </tr>
              @endif
              @foreach($addresses as $address)
              <tr>
                <td>{{ $address->contact_name }}</td>
                <td>{{ $address->full_address }}</td>
                <td>{{ $address->zipcode }}</td>
                <td>{{ $address->contact_phone }}</td>
                <td>
                  <a href="{{ route('addresses.edit', $address->id) }}" class="btn btn-primary">修改</a>
                  <button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">删除</button>

                </td>
              </tr>
              @endforeach

              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>
@endsection

@section('js')
  <script>
    $(document).ready(function () {
       $('.btn-del-address').click(function () {
           var id = $(this).data('id')
           swal({
               title: "确认删除该地址？",
               icon: 'warning',
               buttons: ['取消','确定'],
               dangerMode: true
           }).then(function (willDelete) {
               //确认为true 取消为false
              if (!willDelete){
                  return
              }
              axios.delete('addresses/' + id).then(function (data) {
                 swal({
                     title: "删除成功",
                     icon: "success"
                 }).then(function () {
                     //重载页面
                     location.reload()
                 })
              })

           })
       })
    })
  </script>

@stop
