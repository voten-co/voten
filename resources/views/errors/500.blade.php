@extends('layouts.landing-layout')

@section('head')
	<title>500 Error - {{ config('app.name') }}</title>
	<meta property="og:type" content="article" />
	<meta property="og:title" content="500 Error - {{ config('app.name') }}" />
	<meta property="og:site_name" content="{{ config('app.name') }}" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@voten_co" />
	<meta name="twitter:title" content="500 Error - {{ config('app.name') }}" />

	<meta name="description" content="Sorry, something went wrong!"/>
	<meta property="og:description" content="Sorry, something went wrong!" />
	<meta name="twitter:description" content="Sorry, something went wrong!" />
	<meta property="og:image" content="/imgs/voten-circle.png" />
	<meta name="twitter:image" content="/imgs/voten-circle.png" />
@stop


@section('content')
	<div class="align-center margin-top-3 margin-bottom-3">
		<h1>
			500 Error
		</h1>

		<p>
			I hate to be the one who breaks it to you but there is an error!
		</p>

		@unless(empty($sentryID))
			<!-- Sentry JS SDK 2.1.+ required -->
			<script src="https://cdn.ravenjs.com/3.3.0/raven.min.js"></script>

			<script>
                Raven.showReportDialog({
                    eventId: '{{ $sentryID }}',

                    // use the public DNS (don't include your secret!)
                    dsn: 'https://9aa08237c51a48c7b038ee65b301bdcb@sentry.io/180596'
                });
			</script>
		@endunless
	</div>
@endsection