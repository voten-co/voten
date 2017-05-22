@extends('layouts.email-layout')

@section('head-title')
	<title>New Feedback from {{ '@' . $user->username }}</title>
@endsection

@section('title')
	New Feedback from {{ '@' . $user->username }}
@endsection

@section('content')
	<p>
	    Subject: <b>{{ $feedback->subject }}</b>
	</p>

	<p>
	    {{ $feedback->description }}
	</p>

	<p>
		<b>Voten</b>: Hope you're doing great sully :)
	</p>
@endsection
