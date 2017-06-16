@extends('layouts.landing-layout')

@section('title')
	<title>Reset Password | {{ config('app.name') }}</title>
@stop

@section('content')
    <div class="container-mid user-select">
        <div class="col-7">
            <div class="v-box">
                <h1 class="title align-center">Reset Password</h1>

                <p>
                    Only one more step until getting you a new password. Try not to lose this one, because if you do... Kidding! Nothing would happen :)
                </p>

                <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                    {{ csrf_field() }}

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
						<input type="email" class="form-control" id="email" name="email" value="{{ $email or old('email') }}" placeholder="Email Address..." required>
						@if ($errors->has('email'))
							<small class="text-muted go-red">{{ $errors->first('email') }}</small>
		                @endif
					</div>

                    <div class="form-group">
						<input type="password" class="form-control" id="password" name="password" placeholder="Password..." required>
						@if ($errors->has('password'))
							<small class="text-muted go-red">{{ $errors->first('password') }}</small>
		                @endif
					</div>

                    <div class="form-group">
						<input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="Confirm Password..." required>
						@if ($errors->has('password_confirmation'))
							<small class="text-muted go-red">{{ $errors->first('password_confirmation') }}</small>
		                @endif
					</div>

                    <button class="v-button v-button--green" type="submit">
					    Reset Password
					</button>
                </form>
            </div>
        </div>
    </div>
@endsection
