<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('title')
    @yield('head')

    <meta property="og:locale" content="en_US" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <link href="https://fonts.googleapis.com/css?family=Dosis:300,400,700" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>

    <link href="/icons/css/fontello.7.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
            'env' => config('app.env')
        ]); ?>
    </script>

    <link rel="shortcut icon" href="/imgs/favicon.png">
</head>
<body>
@include('google-analytics')
	<div id="landing">


		<div class="header user-select">
			<div class="logo">
				<a href="/">
					<img src="/imgs/voten-logo.png" alt="{{ config('app.name') }}.co">
					{{ config('app.name') }}
				</a>
				<small>BETA</small>
			</div>

			<div class="right-menu">
				<a href="mailto:{{ config('settings.emails.info') }}" class="item desktop-only">
					<i class="v-icon v-letter go-green"></i>
					Contact
				</a>
				<a href="mailto:{{ config('settings.emails.press') }}" class="item desktop-only">
					<i class="v-icon v-press go-red"></i>
					Press
				</a>
				<a href="/credits" class="item desktop-only">
					<i class="v-icon v-credits go-primary"></i>
					Credits
				</a>

				@if(Auth::check())
					<a href="/" class="v-button v-button--primary">
			            {{ Auth::user()->username }}
			        </a>
				@else
					<a href="/login" class="v-button v-button--primary">
			            Sign in
			        </a>
				@endif

			</div>

		</div>


		@yield('content')


		<footer class="user-select">
			<div class="flex1">
				<h3 class="go-primary">{{ config('app.name') }} 	&#10084;</h3>
				<ul>
					<li><a href="/about">About</a></li>

					@if(Auth::check())
						<li><a href="/logout">Sign out</a></li>
					@else
						<li><a href="/register">Sign Up</a></li>
					@endif

					<li><a href="/tos">Terms of Service</a></li>
					<li><a href="/privacy-policy">Privacy Policy</a></li>
				</ul>
			</div>
			<div class="flex1">
				<h3 class="go-red">Get to know {{ config('app.name') }} </h3>
				<ul>
					<li><a href="mailto:{{ config('settings.email.info') }}">Contact Us</a></li>
					<li><a href="mailto:{{ config('settings.email.press') }}">Press</a></li>
					<li><a href="/credits">Credits</a></li>
					<li><a href="/help">Help Center</a></li>
				</ul>
			</div>
			<div class="flex1">
				<h3 class="go-green">Follow {{ config('app.name') }}</h3>
				<ul>
					<li><a href="{{ config('settings.twitter.url') }}" target="_blank">Twitter</a></li>
					<li><a href="{{ config('settings.twitter.facebook') }}" target="_blank">Facebook</a></li>
				</ul>
			</div>
		</footer>
    </div>

	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
    <script src="{{ mix('js/landing.js') }}"></script>
    @yield('script')
</body>
</html>
