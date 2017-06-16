@extends('layouts.email-layout')

@section('head-title')
	<title>New User Registered</title>
@endsection

@section('title')
	New user registered: {{ '@' . $username }}
@endsection

@section('content')
	<p>We just had another registration by the username: "{{ $username }}"</p>

	<p>
		Hope you're doing great Sully ;)
	</p>

	<p>
		Regards, <br>
		The Voten Team
	</p>
@endsection
