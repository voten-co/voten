<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
    	@section('title')
    		Backend | {{ config('app.anem') }}
    	@show
    </title>

    @yield('head')
    <link href="/icons/css/fontello.6.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.2/css/bulma.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <link rel="shortcut icon" href="/imgs/favicon.png">
</head>

<body>
    {{-- Header  --}}
    <nav class="nav has-shadow">
        <div class="container">
            <div class="nav-left">
                <a class="nav-item is-tab is-hidden-mobile{{ url('backend') == url()->current() ? ' is-active' : '' }}" href="/backend">Dashboard</a>

                <a class="nav-item is-tab is-hidden-mobile{{ url('backend/forbidden-names') == url()->current() ? ' is-active' : '' }}" href="/backend/forbidden-names">Forbiddens</a>

                <a class="nav-item is-tab is-hidden-mobile{{ url('backend/appointed-users') == url()->current() ? ' is-active' : '' }}" href="/backend/appointed-users">Appointed Users</a>

                <a class="nav-item is-tab is-hidden-mobile{{ url('backend/server-control') == url()->current() ? ' is-active' : '' }}" href="/backend/server-control">Server</a>
            </div>

            <span class="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </span>

            <div class="nav-right nav-menu">
                {{-- <a class="nav-item is-tab is-hidden-tablet is-active">Home</a>
                <a class="nav-item is-tab is-hidden-tablet">Features</a>
                <a class="nav-item is-tab is-hidden-tablet">Pricing</a>
                <a class="nav-item is-tab is-hidden-tablet">About</a> --}}
                {{-- <span class="nav-item">
                  <a class="button is-warning" href="/flush-all">
                    <span>Clear Redis Cache</span>
                  </a>
                </span> --}}
                <a class="nav-item is-tab" href="/logout">Log out</a>
                <a class="nav-item is-tab" href="/">Home</a>
            </div>
        </div>
    </nav>

    @if (count($errors) > 0)
        <section class="section container">
            <article class="message is-danger">
                <div class="message-body">
                    <h3><strong>Error:</strong></h3>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </article>
        </section>
    @endif


    {{-- Content  --}}
    @yield('content')




    {{-- Footer  --}}
</body>
</html>
