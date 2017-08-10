@extends('layouts.landing-layout')

@section('title')
	<title>Sign up | Voten</title>
@stop

@section('content')

<div class="container-mid">
	<div class="col-7">
		<div class="social-login-buttons">
	        <a href="/login/google" class="v-button button-google">
	            <i class="v-icon v-google"></i>
	            Connect With Google
	        </a>
		</div>

        <div class="or">
            - or -
        </div>

		<div class="v-box align-center">
			<h1 class="title">Sign up with/without email address</h1>

			<p>
				We're glad you decided to join Voten. Now let's pick you a nice username that is easy to remember:
			</p>

			<form action="{{ url('/register') }}" method="POST" class="align-left">
				{{ csrf_field() }}

				<div class="form-group">
					<input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Username">
					@if ($errors->has('username'))
						<small class="text-muted go-red">{{ $errors->first('username') }}</small>
	                @endif
				</div>

				<div class="form-group">
					<input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Email Address (optional)">
					@if ($errors->has('email'))
						<small class="text-muted go-red">{{ $errors->first('email') }}</small>
	                @endif
				</div>

				<div class="form-group">
					<input id="password" type="password" class="form-control" name="password" required placeholder="Password">
					@if ($errors->has('password'))
						<small class="text-muted go-red">{{ $errors->first('password') }}</small>
	                @endif
				</div>

				<div class="form-group">
					<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Retype Password">
				</div>

				<div class="flex-space">
					<span class="form-notice">By clicking Sign Up, you agree to our <a href="/tos">TOS</a>.</span>
					<button class="v-button v-button--green">Sign up</button>
				</div>
			</form>
		</div>
	</div>
</div>
@stop