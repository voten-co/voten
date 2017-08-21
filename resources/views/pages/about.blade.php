@extends('layouts.landing-layout')


@section('head')
	<title>About | Voten</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="About | Voten" />
	<meta property="og:url" content="{{ config('app.url') }}/about" />
	<meta property="og:site_name" content="Voten" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@voten_co" />
	<meta name="twitter:title" content="About | Voten" />

	<meta name="description" content="We are a small team of developers risen from the world of open-source. We believe in an open and modern Internet. Voten's mission is to give people the power to share their content with not just their friends but the world and interact in real-time."/>
	<meta property="og:description" content="We are a small team of developers risen from the world of open-source. We believe in an open and modern Internet. Voten's mission is to give people the power to share their content with not just their friends but the world and interact in real-time." />
	<meta name="twitter:description" content="We are a small team of developers risen from the world of open-source. We believe in an open and modern Internet. Voten's mission is to give people the power to share their content with not just their friends but the world and interact in real-time." />
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
@endsection


@section('content')
	<div class="about-wrapper mobile-padding">
		<div class="align-center">
			<img src="{{ config('app.url') }}/imgs/voten-circle.png" alt="Voten" class="about-logo margin-bottom-1">

			<h1 class="title">
				Social Bookmarking For The 21st Century
			</h1>
		</div>


		<p>
			We are a small team of developers risen from the world of open-source. We believe in an open and modern Internet.
Voten's mission is to give people the power to share their content with not just their friends but the world and interact in real-time.
		</p>

		<br>
		<h2 class="title">Users are voters</h2>
		<p>
			On Voten users are in charge. They get to decide what deserves more attention by voting. After all the word "Voten" means "vote" in German and Spanish.
		</p>

		<br>
		<h2 class="title">Designed for the 21st century</h2>
		<p>
			Thanks to the power of open-source community, Voten has become the most modern social bookmarking platform on the Internet. It's been developed from scratch to work with latest web technologies such as WebSockets. Voters have full control over their browsing experience. Voten's UI is highly customizable yet deadly simple.
		</p>

		<br>
		<h2 class="title">An open-source project</h2>
		<p>
			 It gets even better: Voten is 100% <a href="https://github.com/voten-co/voten" target="_blank">open-source!</a> Yes, we've got nothing to hide. We also like the idea of giving back something to the open-source community. We also have awesome developer users who directly contribute in Voten's development.
		</p>
	</div>


@endsection