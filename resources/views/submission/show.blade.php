@extends('layouts.guest')


@section('head')
	<title>{{ $submission->title }} - {{ config('app.name') }}</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="{{ $submission->title }} - {{ config('app.name') }}" />
	<meta property="og:url" content="{{ config('app.url') }}/c/{{ $submission->category_name }}/{{ $submission->slug }}" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="{{ config('settings.twitter.name') }}" />
	<meta name="twitter:title" content="{{ $submission->title }} - {{ config('app.name') }}" />

	@if ($submission->type == "text")
		<meta name="description" content="{{ $submission->data['text'] }}"/>
		<meta property="og:description" content="{{ $submission->data['text'] }}" />
		<meta name="twitter:description" content="{{ $submission->data['text'] }}" />
		<meta property="og:image" content="/imgs/voten-circle.png">
		<meta name="twitter:image" content="/imgs/voten-circle.png" />
	@else
		<meta property="og:image" content="{{ $submission->data['thumbnail'] ?? $submission->data['path'] ?? '/imgs/voten-circle.png' }}" />
		<meta name="twitter:image" content="{{ $submission->data['thumbnail'] ?? $submission->data['path'] ?? '/imgs/voten-circle.png' }}" />
	@endif
@stop


@section('content')
	<router-view></router-view>
@endsection


@section('script')
	<script>
		var preload = {
			submission: {!! $submission !!}
		};
	</script>
@endsection