@extends('layouts.no-sidebar')

@section('title')
	<title>Sign up | Voten</title>
@stop

@section('content')
<section class="home-wrapper user-select">
	<nav class="nav has-shadow user-select">
		<div class="container">
			<h1 class="title">
				Sign up
			</h1>

			<div class="flex-center">
				<a href="/" class="margin-right-1">
					<el-button round size="small" type="text">
						Home
					</el-button>
				</a>

				<a href="/login" class="margin-right-1">
					<el-button round size="small" type="text">
						Login 
					</el-button>
				</a>
			</div>
		</div>
	</nav>

	<div id="page" class="home-submissions" >		
		@if ($errors->has('email'))
			<div class="margin-top-1">
				<el-alert type="error" title="{{ $errors->first('email') }}"></el-alert>
			</div>
		@endif

		@if ($errors->has('username'))
			<div class="margin-top-1">
				<el-alert type="error" title="{{ $errors->first('username') }}"></el-alert>
			</div>
		@endif

		@if ($errors->has('password'))
			<div class="margin-top-1">
				<el-alert type="error" title="{{ $errors->first('password') }}"></el-alert>
			</div>
		@endif
		
		@if ($errors->has('g-recaptcha-response'))
			<div class="margin-top-1">
				<el-alert type="error" title="{{ $errors->first('g-recaptcha-response') }}"></el-alert>
			</div>
		@endif

		<el-form role="form" method="POST" action="{{ url('/register') }}" label-position="top"
			label-width="10px">
            {{ csrf_field() }}
			
			<el-form-item label="Username:">
				<el-input
					placeholder="Username..."
					name="username"
				></el-input>
			</el-form-item>

			<el-form-item label="(Optional) Email Address:">
				<el-input
					placeholder="(Optional) Email Address..."
					name="email"
				></el-input>
			</el-form-item>

			<el-form-item label="Password:">
				<el-input
					placeholder="Password..."
					name="password"
					type="password"
				></el-input>
			</el-form-item>

			<el-form-item label="Confirm Password:">
				<el-input
					placeholder="Confirm Password..."
					name="password_confirmation"
					type="password"
				></el-input>
			</el-form-item>

			{{--  Google reCAPTCHA  --}}
			<el-form-item class="margin-top-1">
				<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
			</el-form-item>

			<p>
				By clicking Sign Up, you agree to our <router-link to="/tos">TOS</router-link>.
			</p>

			<div class="margin-top-1">
				<el-button round type="success" size="medium" native-type="submit">
					Sign up
				</el-button> 

				<strong class="margin-sides-1">or</strong>

				<google-login-button></google-login-button>
			</div>
		</el-form>
	</div>
</section>
@stop