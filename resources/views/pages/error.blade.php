@extends('layouts.app')
@section('title','发生错误')

@section('content')
  <div class="row">
    <div class="col-lg-8 offset-lg-2">
      <div class="card">
        <div class="header text-center"><h2>发生错误</h2></div>
        <div class="card-body">

          <p class="card-text ">{{ $msg }}</p>

        </div>
      </div>
    </div>
  </div>
@stop
