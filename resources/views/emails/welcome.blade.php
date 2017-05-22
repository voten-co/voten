@extends('layouts.email-layout')

@section('head-title')
	<title>Welcome To Voten.co</title>
@endsection

@section('title')
	Welcome To Voten {{ '@' . $username }}
@endsection

@section('content')
	<p>We are so excited you joined Voten. Here are a couple of tips to help you get started:</p>

	<ul>
		<li>
			<b>Customize:</b> You can <a href="https://voten.co/{{ '@' . $username }}/settings">customize</a> your account's color, font and your very own home feed to make sure you always get what's best for you.
		</li>

		<li>
			<b>Find Channels:</b> Voten is nothing but a collection of awesome channels (communities) with awesome users like you. Whenever you felt like finding new ones just <a href="https://voten.co/find-channels">go here</a>.
		</li>

		<li>
			<b>Need Help?</b> If you are wondering about a Voten's feature that is confusing to you, just look for it in our <a href="https://voten.co/help">help center</a>.
		</li>
	</ul>

	<p>
		Should you ever encounter problems with your account or forget your password we will contact you at this address.
	</p>

	<p>Happy voting!</p>

	<p>
		Regards, <br>
		The Voten Team
	</p>
@endsection
