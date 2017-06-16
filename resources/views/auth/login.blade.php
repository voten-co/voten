@extends('layouts.landing-layout')

@section('title')
	<title>Sign in | {{ config('app.name') }}</title>
@stop

@section('content')
	<div class="container-mid user-select">
		<div class="col-7">
			<div class="social-login-buttons">
				@if (config('settings.facebook.secret') && config('settings.facebook.client_id'))
					<a href="/login/facebook" class="v-button button-fb">
						<i class="v-icon v-facebook"></i>
						Connect With Facebook
					</a>
				@endif

				@if (config('settings.google.secret') && config('settings.google.client_id'))
					<a href="/login/google" class="v-button button-google">
						<i class="v-icon v-google"></i>
						Connect With Google
					</a>
				@endif
			</div>

	        <div class="or">
	            - or -
	        </div>

			<div class="v-box align-center">
				<h1 class="title">Sign in with username and password</h1>

				<p>
					Thank you for being a part of {{ config('app.name') }} community
				</p>

				<form action="{{ url('/login') }}" method="POST" class="align-left">
					{{ csrf_field() }}

					<div class="form-group">
						<input type="text" class="form-control" id="username" name="username" placeholder="Username..." required>
						@if ($errors->has('username'))
							<small class="text-muted go-red">{{ $errors->first('username') }}</small>
		                @endif
					</div>

					<div class="form-group">
						<input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
						@if ($errors->has('password'))
							<small class="text-muted go-red">{{ $errors->first('password') }}</small>
		                @endif
					</div>

	                <div class="form-group ui form">
		                <div class="inline field">
		                    <div class="ui toggle checkbox">
		                        <input type="checkbox" class="hidden" name="remember">
		                        <label>Remember Me</label>
		                    </div>
		                </div>
		            </div>

					<div class="flex-space">
						<a class="v-button" href="{{ url('/password/reset') }}">Forgot my password</a>
						<button class="v-button v-button--green">Sign In</button>
					</div>
				</form>
			</div>

			<p class="align-center go-gray">
				Not a member? Then let's get you <a href="/register">signed up</a>.
			</p>
		</div>
	</div>
@stop
