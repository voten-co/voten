@extends('layouts.app')


@section('content')
	<router-view></router-view>

{{--  <keep-alive>	
	<router-view></router-view>
</keep-alive>  --}}

@endsection


@section('head')
	<title>Voten - Social Bookmarking For The 21st Century</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Voten - Social Bookmarking For The 21st Century" />
	<meta property="og:url" content="{{ config('app.url') }}" />
	<meta property="og:site_name" content="Voten" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@voten_co" />
	<meta name="twitter:title" content="Voten - Social Bookmarking For The 21st Century" />

	<meta name="description" content="A Modern, real-time, open-source, beautiful, deadly simple and warm community."/>
	<meta property="og:description" content="A Modern, real-time, open-source, beautiful, deadly simple and warm community." />
	<meta name="twitter:description" content="A Modern, real-time, open-source, beautiful, deadly simple and warm community." />
	<meta property="og:image" content="{{ config('app.url') }}/imgs/voten-circle.png">
	<meta name="twitter:image" content="{{ config('app.url') }}/imgs/voten-circle.png" />

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
