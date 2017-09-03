@extends('layouts.guest')


@section('head')
    <title>{{ $help->title }} - Voten</title>
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $help->title }} - Voten" />
    <meta property="og:url" content="{{ config('app.url') }}/help/{{ $help->id }}" />
    <meta property="og:site_name" content="Voten" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@voten_co" />
    <meta name="twitter:title" content="{{ $help->title }} - Voten" />

    <meta name="description" content="{{ $help->body }}"/>
    <meta property="og:description" content="{{ $help->body }}" />
    <meta name="twitter:description" content="{{ $help->body }}" />
    <meta property="og:image" content="/imgs/voten-circle.png">
    <meta name="twitter:image" content="/imgs/voten-circle.png" />
@stop


@section('content')
    <router-view></router-view>
@endsection


@section('script')
    <script>
        var preload = {
            help: {!! $help !!}
        };
    </script>
@endsection