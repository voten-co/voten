@extends('layouts.guest')


@section('head')
	<title>#{{ $category->name }} - {{ config('app.name') }}</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="#{{ $category->name }} - {{ config('app.name') }}" />
	<meta property="og:url" content="{{ config('app.url') }}/c/{{ $category->name }}" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="{{ config('services.twitter.name') }}" />
	<meta name="twitter:title" content="#{{ $category->name }} - {{ config('app.name') }}" />

	<meta name="description" content="{{ $category->description }}"/>
	<meta property="og:description" content="{{ $category->description }}" />
	<meta name="twitter:description" content="{{ $category->description }}" />
	<meta property="og:image" content="{{ $category->avatar }}" />
	<meta name="twitter:image" content="{{ $category->avatar }}" />
@stop


@section('content')
	<router-view></router-view>
@endsection


@section('script')
	<script>
		var preload = {
			category: {!! $category !!},
			submissions: {!! $submissions->toJson() !!}
		};
	</script>
@endsection