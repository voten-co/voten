@extends('layouts.no-sidebar') 

@section('title')
	<title>Forgot my password</title>
@stop 

@section('content')
<section class="home-wrapper user-select">
	<nav class="nav has-shadow user-select">
		<div class="container">
			<h1 class="title">
				Request a password reset
			</h1>

			<div class="flex-center">
				<a href="/" class="margin-right-1">
					<el-button round size="small" type="text">
						Home
					</el-button>
				</a>

				<a href="/register" class="margin-right-1">
					<el-button round size="small" type="text">Sign up</el-button>
				</a>

				<a href="/login" class="margin-right-1">
					<el-button round size="small" type="text">Login</el-button>
				</a>
			</div>
		</div>
	</nav>

	<div id="page" class="home-submissions">
		@if (session('status'))
			<el-alert type="success" title="{{ session('status') }}" :closable="false" show-icon></el-alert>
		@endif
		
		<p>
			Enter your registered email address below and we'll email you a link to reset your password.
		</p>

		<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
			{{ csrf_field() }}

			<el-input
				placeholder="Email Address..."
				name="email"
				value="{{ old('email') }}"
			></el-input>

			@if ($errors->has('email'))
				<div class="margin-top-1">
					<el-alert type="error" title="{{ $errors->first('email') }}"></el-alert>
				</div>
			@endif

			<div class="margin-top-1">
				<el-button round type="success" size="medium" native-type="submit">
					Send Password Reset Link
				</el-button>
			</div>
		</form>
	</div>
</section>
@endsection