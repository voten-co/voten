@extends('layouts.no-sidebar')

@section('title')
	<title>Login | Voten</title>
@stop

@section('content')
<section class="home-wrapper user-select">
	<nav class="nav has-shadow user-select">
		<div class="container">
			<h1 class="title">
				Login
			</h1>

			<div class="flex-center">
				<a href="/" class="margin-right-1">
					<el-button round size="small" type="text">
						Home
					</el-button>
				</a>
				
				<a href="/register" class="margin-right-1">
					<el-button round size="small" type="text">
						Sign up
					</el-button>
				</a>
			</div>
		</div>
	</nav>

	<div id="page" class="home-submissions" >	
		<p>
			Thank you for being a part of Voten community
		</p>

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

		<el-form method="POST" action="{{ url('/login') }}" label-position="top"
			label-width="10px">
            {{ csrf_field() }}
			
			<el-form-item label="Username or Email Address:">
				<el-input
					placeholder="Username or Email Address..."
					name="username"
				></el-input>
			</el-form-item>

			<el-form-item label="Password:">
				<el-input
					placeholder="Password..."
					name="password"
					type="password"
				></el-input>
			</el-form-item>

			<div class="margin-top-1">
				<el-button round type="success" size="medium" native-type="submit">
					Login
				</el-button> 

				<strong class="margin-sides-1">or</strong>

				<google-login-button></google-login-button>
			</div>
		</el-form>
	</div>
</section>
@stop
