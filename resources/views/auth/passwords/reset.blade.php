@extends('layouts.guest')

@section('title')
	<title>Reset Password | Voten</title>
@stop

@section('content')
<section class="home-wrapper user-select">
	<nav class="nav has-shadow user-select">
		<div class="container">
			<h1 class="title">
				Reset Password
			</h1>

			<div class="flex-center">
				<a href="/register" class="margin-right-1">
					<el-button round size="small" type="text">Sign up</el-button>
				</a>

				<a href="/login" class="margin-right-1">
					<el-button round size="small" type="text">Login</el-button>
				</a>
			</div>
		</div>
	</nav>

	<div id="page" class="home-submissions" @keyup.enter="submit">
		@if (session('status'))
			<el-alert type="success" title="{{ session('status') }}" :closable="false" show-icon></el-alert>
		@endif
		
		<p>
            Only one more step untill getting you a new password. Try not to lose this one, because if you do... Kidding! Nothing would happen :)
        </p>

		<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
            {{ csrf_field() }}
            
            <input type="hidden" name="token" value="{{ $token }}">

			<el-input class="margin-top-1"
				placeholder="Email Address..."
				name="email"
				value="{{ $email or old('email') }}"
			></el-input>

			@if ($errors->has('email'))
				<div class="margin-top-1">
					<el-alert type="error" title="{{ $errors->first('email') }}"></el-alert>
				</div>
            @endif
            
            <el-input class="margin-top-1"
                placeholder="Password..."
                name="password"
                type="password"
            ></el-input>

            @if ($errors->has('password'))
                <div class="margin-top-1">
                    <el-alert type="error" title="{{ $errors->first('password') }}"></el-alert>
                </div>
            @endif
            
            <el-input class="margin-top-1"
                placeholder="Confirm Password..."
                name="password_confirmation"
                type="password"
            ></el-input>

            @if ($errors->has('password_confirmation'))
                <div class="margin-top-1">
                    <el-alert type="error" title="{{ $errors->first('password_confirmation') }}"></el-alert>
                </div>
            @endif

			<div class="margin-top-1">
				<el-button round type="success" size="medium" native-type="submit">
					Reset Password
				</el-button>
			</div>
		</form>
	</div>
</section>
@endsection
