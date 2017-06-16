@extends('layouts.app')


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
	<meta name="twitter:site" content="{{ config('settings.twitter.name') }}" />
	<meta name="twitter:title" content="{{ config('app.name') }} - {{ config('settings.title') }}" />

	<meta name="description" content="{{ config('settings.about_description') }}"/>
	<meta property="og:description" content="{{ config('settings.about_description') }}" />
	<meta name="twitter:description" content="{{ config('settings.about_description') }}" />
	<meta property="og:image" content="{{ config('app.url') }}/imgs/voten-circle.png">
	<meta name="twitter:image" content="{{ config('app.url') }}/imgs/voten-circle.png" />

	<script type="application/ld+json">
		{
		    "@context": "http://schema.org",
		    "@type": "Organization",
		    "url": "{{ config('app.url') }}",
		    "name": "{{ config('app.name') }}",
		    "logo": {
	            "@type": "ImageObject",
	            "url": "{{ config('app.url') }}/imgs/voten-circle.png",
	            "width": "512",
	            "height": "512"
	        },
		    "sameAs": [
		        "{{ config('settings.facebook.url') }}",
		        "{{ config('settings.twitter.url') }}"
		    ]
		}
	</script>
@endsection
