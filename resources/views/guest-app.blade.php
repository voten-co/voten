<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
    	Home | Voten
    </title>

    <link href="/icons/css/fontello.6.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

	{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script> --}}
	{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/2.0.3/vue-router.min.js"></script> --}}


    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
            'env' => env('APP_ENV', 'production')
        ]); ?>
    </script>

    <link rel="shortcut icon" href="/imgs/favicon.png">
</head>

<body>

<div id="guest-app" :class="{ 'background-white': Store.contentRouter != 'content' }" @scroll="scrolled">
	@include('app-header')

	<div class="v-content-wrapper">
		<div class="v-side" v-show="sidebar">
		    <guest-sidebar></guest-sidebar>
		</div>

		<search-modal v-if="Store.contentRouter == 'search'" :sidebar="sidebar"></search-modal>

		<div class="v-content" v-show="Store.contentRouter == 'content'">
			<transition name="fade">
                <rules v-if="modalRouter == 'rules'" :sidebar="sidebar"></rules>
                <moderators v-if="modalRouter == 'moderators'" :sidebar="sidebar"></moderators>
                <markdown-guide v-if="modalRouter == 'markdown-guide'" :sidebar="sidebar"></markdown-guide>
			</transition>

			<div :class="{ 'v-blur-blackandwhite': smallModal }">
                <router-view></router-view>
            </div>
		</div>
	</div>
</div>

<script>
    var auth = {
        font: 'Lato',
        nsfw: {{ 'false' }},
        nsfwMedia: {{ 'false' }},
        sidebar_color: 'Gray',
        isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }},
        <?php
            if (isMobileDevice()) {
                $submission_small_thumbnail = 'false';
            } else {
                $submission_small_thumbnail = 'true';
            }
        ?>
        submission_small_thumbnail: {{ $submission_small_thumbnail }},
        isGuest: {{ 'false' }}
    }
</script>

<script src="{{ mix('/js/app.js') }}"></script>

</body>
</html>
