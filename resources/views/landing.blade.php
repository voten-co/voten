@extends('layouts.landing-layout')

@section('title')
	<title>{{ config('app.name') }} | Where your vote matters</title>
	<meta property="og:title" content="{{ config('app.name') }} | Where your vote matters" />
	<meta property="og:image" content="/imgs/voten-circle.png">
	<meta name="description" content="{{ config('settings.description') }}"/>
@stop

@section('content')
	<div class="landing-layout-gray">
		<div class="landing-wrapper align-center">
			<h1 class="home-title-big">
				Social Bookmarking For The 21st Century
			</h1>

			<h2 class="sub-title">
				Real-time, beautiful, customizable yet simple
			</h2>

			<div class="invite-box">
				<a href="/register" class="v-button v-button--green v-button--block register-button-big">
					Sign up in just seconds with or without an email address
				</a>
			</div>
		</div>
	</div>







	{{-- Customizable Design --}}
	<div class="landing-layout-white">
		<div class="landing-wrapper landing-flex">
			<img src="/landing/voten-submission.jpg" alt="Customizable Design" class="shadow-box left">

			<div class="right">
				<h1>
					Customizable Design
				</h1>

				<p>
					Which font is the best for reading? Which color? Well, in {{ config('app.name') }} not only you can name it but you can also make it be!
				</p>

				<p>
					More than 20 fonts to choose from, and countless color schemes. That means giving your browsing workflow a fresh
					look in matter of seconds.
				</p>
			</div>
		</div>
	</div>






	{{-- Search --}}
	<div class="landing-layout-gray">
		<div class="landing-wrapper align-center">
			<div class="right">
				<h1>
					Intelligent Search
				</h1>

				<p class="margin-bottom-3">
					Search through channels, users, submissions and even comments. We've made sure you will find the content you want.
				</p>
			</div>

			<img src="/landing/search.jpg" alt="voten-search" class="shadow-box left margin-top-2 margin-bottom-3 max-width-50">
		</div>
	</div>








	{{-- Notifications --}}
	<div class="landing-layout-white">
		<div class="landing-wrapper landing-flex">
			<img src="/landing/notification.jpg" alt="notification" class="shadow-box left border-radius-8">

			<div class="right">
				<h1>
					Real-time
				</h1>

				<p>
					Real-time Notifications that make sure you get notified instantly. No more refreshes to see what’s new.
				</p>

				<p>
					See who else is reading the same submission you are, comment and even if you needed to chat in private,
					you can do that using our secure and real-time messaging system.
				</p>
			</div>
		</div>
	</div>









{{-- Best Features --}}
	<div class="landing-layout-gray">
		<div class="landing-wrapper">
			<h1 class="align-center">
				{{ config('app.name') }} features at a glance
			</h1>

			<div class="landing-flex-baseline">
				<div class="left">
					<ul>
						<li>
							<b>Voting System</b> that makes sure the best content always wins. This makes sure the power is literally in people’s hands instead of media.
						</li>

						<li>
							<b>Markdown Editor</b> designed for people who need something more that just a plain text in their comments, submissions and messages!
						</li>

						<li>
							<b>Fast and Intelligent Search</b> that makes sure you find what you’re looking for. Currently supports channels, users, submissions and even comments!
						</li>

						<li>
							<b>Highly Customizable</b> apps that allow you pick your very own favored font and colors. This means getting a fresh look in matter of seconds.
						</li>

						<li>
							<b>Real-time Notifications</b> that make sure you get notified instantly. No more refreshes to see what’s new.
						</li>

						<li>
							<b>Rich Submission types</b> such as photo, photo albums, text(with Markdown support) and link which is able to identify the type of the external URL (playable videos and etc)
						</li>
					</ul>
				</div>

				<div class="right">
					<ul>
						<li>
							Real-time and secure <b>Messaging</b> system which also supports Markdown syntax.
						</li>

						<li>
							A personal sidebar that guarantees a <b>Smooth Browsing</b> experience for every taste.
						</li>

						<li>
							Nested <b>Commenting System</b> that supports Markdown syntax and works completely in real-time.
						</li>

						<li>
							Handy <b>Moderation Tools</b> for moderators. Moderation is as important as any part in channels, so we created such awesome tools exclusively for moderators.
						</li>

						<li>
							Manage your very own <b>Home Feed</b> by subscribing to your favorite channels, and then filtering those results. Don’t wanna see what you already voted on? Sure, why not.
						</li>

						<li>
							<b>Bookmark</b> submissions, comments, channels and even users with one single click.
							Bookmarking has never been easier.
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection
