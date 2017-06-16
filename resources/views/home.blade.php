@extends('layouts.guest')


@section('content')
	<router-view></router-view>
@endsection


@section('head')
	<title>{{ config('app.name') }} - {{ config('settings.title') }}</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="{{ config('app.name') }} - {{ config('settings.title') }}" />
	<meta property="og:url" content="{{ config('app.url') }}" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@your_twitter" />
	<meta name="twitter:title" content="{{ config('app.name') }} - {{ config('settings.title') }}" />

	<meta name="description" content="{{ config('settings.description') }}"/>
	<meta property="og:description" content="{{ config('settings.description') }}" />
	<meta name="twitter:description" content="{{ config('settings.description') }}" />
	<meta property="og:image" content="{{ config('app.url') }}/imgs/voten-circle.png">
	<meta name="twitter:image" content="{{ config('app.url') }}/imgs/voten-circle.png" />

	<script type="application/ld+json">
	{
	    "@context": "http://schema.org",
	    "@type": "WebSite",
	    "url": "{{ config('app.url') }}",
	    "name": "{{ config('app.name') }}",
	    "logo": "{{ config('app.url') }}/imgs/voten-circle.png",
	    "sameAs": [
	        "{{ config('settings.facebook.url') }}",
	        "{{ config('settings.twitter.url') }}"
	    ]
	}
	</script>
@endsection



@section('script')
	<script>
		var preload = {
			submissions: {!! $submissions->toJson() !!}
		};
	</script>
@endsection
