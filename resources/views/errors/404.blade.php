@extends('layouts.landing-layout')

@section('head')
	<title>Not Found 404 - {{ config('app.name') }}</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Not Found 404 - {{ config('app.name') }}" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="{{ config('services.twitter.name') }}" />
	<meta name="twitter:title" content="Not Found 404 - {{ config('app.name') }}" />

	<meta name="description" content="Sorry, that page doesn’t exist!"/>
	<meta property="og:description" content="Sorry, that page doesn’t exist!" />
	<meta name="twitter:description" content="Sorry, that page doesn’t exist!" />
	<meta property="og:image" content="/imgs/voten-circle.png" />
	<meta name="twitter:image" content="/imgs/voten-circle.png" />
@stop


@section('content')
	<div class="align-center margin-top-3 margin-bottom-3">
		<h1>
			Not found 404
		</h1>

		<p>
			I hate to be the one who breaks it to you but you've been given a wrong address!
		</p>
	</div>
@endsection