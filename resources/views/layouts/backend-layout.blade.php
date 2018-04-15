<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
    	@section('title')
    		Backend | Voten
    	@show
    </title>

    @yield('head')
        <link href="/icons/css/fontello.6.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.5.0/css/bulma.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ mix('/css/admin.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('env-to-js-data')

    <link rel="shortcut icon" href="{{ config('app.favicon') }}">
</head>

<body>

<div id="backend">
    @include('backend.header')

    @include('backend.messages')

    @yield('content')
</div>

@include('php-to-js-data')

<script src="{{ mix('/js/manifest.js') }}"></script>
<script src="{{ mix('/js/vendor.js') }}"></script>
{{--  <script src="{{ mix('/js/app.js') }}"></script>  --}}

</body>
</html>
