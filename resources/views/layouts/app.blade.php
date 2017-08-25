<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('head')
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
            'env' => config('app.env'),
            'pusherKey' => config('broadcasting.connections.pusher.key'),
            'pusherCluster' => config('broadcasting.connections.pusher.options.cluster'),
        ]); ?>
    </script>

    <link rel="shortcut icon" href="/imgs/favicon.png">
    @include('user.user-style')
</head>

<body>
@include('google-analytics')

<div id="voten-app" :class="{ 'background-white': Store.contentRouter != 'content' }">
    @include('app-header')

    <div class="v-content-wrapper">
		<div class="v-side {{ settings('sidebar_color') }}" v-show="sidebar">
		    <sidebar></sidebar>
		</div>

		<notifications v-show="Store.contentRouter == 'notifications'"></notifications>
		<messages v-show="Store.contentRouter == 'messages'" :sidebar="sidebar"></messages>
		<search-modal v-if="Store.contentRouter == 'search'" :sidebar="sidebar"></search-modal>

        <div class="v-content" id="v-content" v-show="Store.contentRouter == 'content'" @scroll="scrolled">
            <transition name="fade">
                <report-submission v-if="modalRouter == 'report-submission'" :submission="reportSubmissionId" :category="reportCategory" :sidebar="sidebar"></report-submission>
                <report-comment v-if="modalRouter == 'report-comment'" :comment="reportCommentId" :category="reportCategory" :sidebar="sidebar"></report-comment>
                <feedback v-if="modalRouter == 'feedback'" :sidebar="sidebar"></feedback>
                <rules v-if="modalRouter == 'rules'" :sidebar="sidebar"></rules>
                <moderators v-if="modalRouter == 'moderators'" :sidebar="sidebar"></moderators>
                <keyboard-shortcuts-guide v-if="modalRouter == 'keyboard-shortcuts-guide'" :sidebar="sidebar"></keyboard-shortcuts-guide>
                <markdown-guide v-if="modalRouter == 'markdown-guide'" :sidebar="sidebar"></markdown-guide>
            </transition>
            <crop-modal v-if="modalRouter == 'crop-user'" :sidebar="sidebar" :type="'user'"></crop-modal>
            <crop-modal v-if="modalRouter == 'crop-category'" :sidebar="sidebar" :type="'category'"></crop-modal>

            <div :class="{ 'v-blur-blackandwhite': smallModal }">
                @yield('content')
            </div>
        </div>
    </div>

    <scroll-button></scroll-button>
</div>

<script>
    var auth = {
        id: '{{ Auth::user()->id }}',
        bio: '{!! strToHex(Auth::user()->bio) !!}',
        name: '{{ Auth::user()->name }}',
        email: '{{ Auth::user()->email }}',
        color: '{{ Auth::user()->color }}',
        avatar: '{{ Auth::user()->avatar }}',
        location: '{{ Auth::user()->location }}',
        username: '{{ Auth::user()->username }}',
        created_at: '{{ Auth::user()->created_at }}',
        font: '{{ settings('font') }}',
        nsfw: {{ settings('nsfw') ? 'true' : 'false' }},
        nsfwMedia: {{ settings('nsfw_media') ? 'true' : 'false' }},
        sidebar_color: '{{ settings('sidebar_color') }}',
        notify_comments_replied: {{ settings('notify_comments_replied') ? 'true' : 'false' }},
        notify_submissions_replied: {{ settings('notify_submissions_replied') ? 'true' : 'false' }},
        notify_mentions: {{ settings('notify_mentions') ? 'true' : 'false' }},
        exclude_upvoted_submissions: {{ settings('exclude_upvoted_submissions') ? 'true' : 'false' }},
        exclude_downvoted_submissions: {{ settings('exclude_downvoted_submissions') ? 'true' : 'false' }},
        isMobileDevice: {{ isMobileDevice() ? 'true' : 'false' }},
        <?php
            if (isMobileDevice()) {
                $submission_small_thumbnail = 'false';
            } else {
                $submission_small_thumbnail = 'true';
            }
        ?>
        submission_small_thumbnail: {{ $submission_small_thumbnail }},
        info: {
        	website: '{{ Auth::user()->info['website'] }}',
        	twitter: '{{ Auth::user()->info['twitter'] }}'
        },
        stats: {!! Auth::user()->stats() !!},
        isGuest: {{ 'false' }}
    };

    var preload = {};
</script>

@yield('script')
	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
	<script src="{{ mix('/js/app.js') }}"></script>
@yield('footer')

</body>
</html>
