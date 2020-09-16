@extends('layouts.app')

@section('title', ($address->id? '修改' : '新增'). '收货地址')

@section('content')
  <div class="row {{ route_class() }}" >
    <div class="col-md-8 offset-md-2">

      <div class="card">
        <div class="card-header">
          <h2 class="text-center">
            {{ ($address->id? '修改' : '新增'). '收货地址' }}
          </h2>
        </div>
        <div class="card-body">
          {{--输出错误--}}
          @if(count($errors)>0)
            <div class="alert alert-danger">
              <h4>发生错误：</h4>
              <ul>

                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach

              </ul>
            </div>
          @endif
          {{--组件使用内联样式--}}
          <user-address-select inline-template>
            @if($address->id)
            <form action="{{ route('addresses.update',$address->id) }}" method="post">
                {{ method_field('PATCH') }}
            @else
            <form action="{{route('addresses.store')}}" method="post">
            @endif
              {{ csrf_field() }}
              {{--省市区组件--}}
              {{--vue可以把json数据转化为数组--}}
                <select-district :init-value="{{ json_encode([old('province_name', $address->province_name), old('city_name',$address->city_name),
                old('district_name',$address->district_name)]) }}" v-on:changed-address="onChangeAddress"></select-district>
                <input type="hidden" name="province_name" v-model="province">
                <input type="hidden" name="city_name" v-model="city">
                <input type="hidden" name="district_name" v-model="district">
                <div class="form-group row">
                  <label class="col-form-label col-sm-3 text-sm-right">区地址&nbsp;</label>
                  <div class="col-sm-9 form-control">
                    <p type="text" >@{{ address }}</p>
                  </div>
                </div>

              <div class="form-group row">
                <label class="col-sm-3 text-sm-right">街道住所地址</label>
                <input
                  type="text"
                  name="strict" id="" class="form-control col-sm-9" value="{{ old('strict', $address->strict) }}" >
              </div>
              <div class="form-group row">
                <label class="col-sm-3 text-sm-right">收货人</label>
                <input
                  type="text"
                  name="contact_name" id="" class="form-control col-sm-9" value="{{ old('contact_name', $address->contact_name) }}">
              </div>
              <div class="form-group row">
                <label class="col-sm-3 text-sm-right">手机号</label>
                <input
                  type="phone"
                  name="contact_phone" id="" class="form-control col-sm-9" value="{{ old('contact_phone', $address->contact_phone) }}" >
              </div>
              <div class="form-group row text-center">
                <div class="col-12">
                  <button type="submit" class="btn btn-primary" >提交</button>
                </div>
              </div>
            </form>
          </user-address-select>
        </div>
      </div>
    </div>
  </div>


@stop
