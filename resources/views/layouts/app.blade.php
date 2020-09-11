<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content={{ csrf_token()}}>
    <title>@yield('title','Laravel_Shop')</title>
    {{--样式--}}
    <link href="{{ mix('css/app.css') }}"rel="stylesheet">
</head>
<body>
    <div id="app" class="{{ route_class() }}-page">
        @include('layouts._header')
        <div class="content">
            @yield('content')
        </div>
        @include('layouts._footer')
    </div>
    {{--js脚本--}}
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
