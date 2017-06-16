@extends('layouts.landing-layout')


@section('head')
	<title>About | Voten</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="About | {{ config('app.name') }}" />
	<meta property="og:url" content="{{ config('app.url') }}/about" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="{{ config('settings.twitter.name') }}" />
	<meta name="twitter:title" content="About | {{ config('app.name') }}" />

	<meta name="description" content="{{ config('settings.about_description') }}"/>
	<meta property="og:description" content="{{ config('settings.about_description') }}" />
	<meta name="twitter:description" content="{{ config('settings.about_description') }}" />
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


@section('content')
	<div class="about-wrapper mobile-padding">
		<div class="align-center">
			<img src="{{ config('app.url') }}/imgs/voten-circle.png" alt="{{ config('app.name') }}" class="about-logo margin-bottom-1">

			<h1 class="title">
				Social Bookmarking For The 21st Century
			</h1>
		</div>


		<p>
			We are a small team of developers risen from the world of open-source. We believe in an open and modern Internet.
{{ config('app.name') }}'s mission is to give people the power to share their content with not just their friends but the world and interact in real-time.
		</p>

		<br>
		<h2 class="title">Users are voters</h2>
		<p>
			On {{ config('app.name') }} users are in charge. They get to decide what deserves more attention by voting. After all the word "{{ config('app.name') }}" means "vote" in German and Spanish.
		</p>

		<br>
		<h2 class="title">Designed for the 21st century</h2>
		<p>
			Thanks to the power of open-source community, {{ config('app.name') }} has become the most modern social bookmarking platform on the Internet. It's been developed from scratch to work with latest web technologies such as WebSockets. Voters have full control over their browsing experience. {{ config('app.name') }}'s UI is highly customizable yet deadly simple.
		</p>
	</div>


@endsection