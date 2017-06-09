@extends('layouts.landing-layout')

@section('title')
	<title>Forgot my password | Voten</title>
@stop

@section('content')
    <div class="container-mid user-select">
        <div class="col-7">

            @if (session('status'))
                <div class="v-status v-status--success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="v-box">
				<h1 class="title align-center">Request a password reset</h1>

				<p>
					Enter your registered email address below and we'll email you a link to reset your password.
				</p>

                <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
					{{ csrf_field() }}

					<div class="form-group">
						<input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Email Address..." required>
						@if ($errors->has('email'))
							<small class="text-muted go-red">{{ $errors->first('email') }}</small>
		                @endif
					</div>

					<button class="v-button v-button--green" type="submit">Send Password Reset Link</button>
				</form>
			</div>
        </div>
    </div>
@endsection
