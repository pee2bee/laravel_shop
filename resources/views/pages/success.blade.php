@extends('layouts.app')
@section('title','操作成功')

@section('content')
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card">
        <div class="header text-center"><h2>操作成功</h2></div>
        <div class="card-body">


          <div class="card-body text-center">
            <h3>{{ $msg }}</h3>
            <a class="btn btn-primary" href="{{ route('root') }}">返回首页</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
