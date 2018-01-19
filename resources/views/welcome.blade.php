@extends('layouts.app')


@section('content')
	<router-view></router-view>
@endsection


@section('head')
	<title>{{ config('app.title') }}</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="{{ config('app.title') }}" />
	<meta property="og:url" content="{{ config('app.url') }}" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="{{ config('app.twitter_username') }}" />
	<meta name="twitter:title" content="{{ config('app.title') }}" />

	<meta name="description" content="{{ config('app.description') }}"/>
	<meta property="og:description" content="{{ config('app.description') }}" />
	<meta name="twitter:description" content="{{ config('app.description') }}" />
	<meta property="og:image" content="{{ config('app.url') }}/imgs/{{ config('app.logo') }}">
	<meta name="twitter:image" content="{{ config('app.url') }}/imgs/{{ config('app.logo') }}" />

	<script type="application/ld+json">
	 {
		 "@context": "http://schema.org",
		 "@type": "WebSite",
		 "url": "https://voten.co",
		 "name": "Voten",
			"publisher": {
			 "@type": "Organization",
		  "logo": {
			  "@type": "ImageObject",
				 "url": "https://voten.co/imgs/voten-circle.png",
				 "name": "Voten",
				 "height": "457",
				 "width": "457"
				}
			},
		 "sameAs": [
			 "https://www.facebook.com/voten.co/",
			 "https://twitter.com/voten_co"
		 ],
		 "potentialAction": {
			"@type": "SearchAction",
			"target": "https://voten.co/?search={search_term_string}",
			"query-input": "required name=search_term_string"
		 }
	 }
	 </script>

	<script type="application/ld+json">
	{
	  "@context":"http://schema.org",
	  "@type":"ItemList",
	  "itemListElement":[
		{
		  "@type":"SiteNavigationElement",
		  "position":1,
		  "name": "Hot",
		  "url":"https://voten.co/?sort=hot"
		},
		{
		  "@type":"SiteNavigationElement",
		  "position":2,
		  "name": "New",
		  "url":"https://voten.co/?sort=new"
		},
		{
		  "@type":"SiteNavigationElement",
		  "position":3,
		  "name": "Rising",
		  "url":"https://voten.co/?sort=rising"
		},
		{
		  "@type":"SiteNavigationElement",
		  "position":4,
		  "name": "#technology",
		  "url":"https://voten.co/c/technology"
		},
		{
		  "@type":"SiteNavigationElement",
		  "position":5,
		  "name": "#news",
		  "url":"https://voten.co/c/news"
		},
		{
		  "@type":"SiteNavigationElement",
		  "position":6,
		  "name": "#funny",
		  "url":"https://voten.co/c/funny"
		},
		{
		  "@type":"SiteNavigationElement",
		  "position":7,
		  "name": "#politics",
		  "url":"https://voten.co/c/politics"
		}
	  ]
	}
	</script>
@endsection
