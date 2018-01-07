<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('head')

    @yield('title')

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.Laravel = @json([
            'csrfToken' => csrf_token(),
            'env' => config('app.env'),
            'pusherKey' => config('broadcasting.connections.pusher.key'),
            'pusherCluster' => config('broadcasting.connections.pusher.options.cluster'),
        ])
    </script>

    <link rel="shortcut icon" href="/imgs/favicon.png">
</head>

<body>
@include('google-analytics')

<div id="voten-app" :class="{ 'background-white': Store.contentRouter != 'content' }">
    <vue-progress-bar></vue-progress-bar>

    <div class="v-content-wrapper">
		<left-sidebar></left-sidebar>

		<search-modal v-if="Store.contentRouter == 'search'"></search-modal>

        <div class="v-content" id="v-content" v-show="Store.contentRouter == 'content'" @scroll="scrolled">
            @yield('content')
        </div>

        <guest-sidebar v-show="showRightSidebar"></guest-sidebar>
    </div>

    <login-modal v-if="Store.showAuthinticationModal" :visible.sync="Store.showAuthinticationModal"></login-modal>
    <markdown-guide v-if="Store.showMarkdownGuide" :visible.sync="Store.showMarkdownGuide"></markdown-guide>
    <keyboard-shortcuts-guide v-if="Store.showKeyboardShortcutsGuide" :visible.sync="Store.showKeyboardShortcutsGuide"></keyboard-shortcuts-guide>
</div>

@include('php-to-js-data')

@yield('script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
	<script src="{{ mix('/js/app.js') }}"></script>
@yield('footer')

</body>
</html>
