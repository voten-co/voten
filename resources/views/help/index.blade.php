@extends('layouts.guest')


@section('head')
    <title>Help Center - Voten</title>
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Help Center - Voten" />
    <meta property="og:url" content="{{ config('app.url') }}/help" />
    <meta property="og:site_name" content="Voten" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@voten_co" />
    <meta name="twitter:title" content="Help Center - Voten" />

    <meta name="description" content="Get instant answers for the most common questions and learn how to take the most out of Voten."/>
    <meta property="og:description" content="Get instant answers for the most common questions and learn how to take the most out of Voten." />
    <meta name="twitter:description" content="Get instant answers for the most common questions and learn how to take the most out of Voten." />
    <meta property="og:image" content="/imgs/voten-circle.png">
    <meta name="twitter:image" content="/imgs/voten-circle.png" />
@stop


@section('content')
    <router-view></router-view>
@endsection


@section('script')
    <script>
        var preload = {
            recentQuestions: {!! $recent_questions !!},
            commonQuestions: {!! $common_questions !!}
        };
    </script>
@endsection