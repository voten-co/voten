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

<div id="voten-app" :class="{ 'background-white': Store.contentRouter != 'content' }" :style="{ fontFamily: auth.font }">
    <div class="v-content-wrapper" :class="{ 'v-blur v-blur-wrapper': smallModal }">
        <left-sidebar></left-sidebar>

        <notifications v-show="Store.contentRouter == 'notifications'"></notifications>
        <messages v-show="Store.contentRouter == 'messages'"></messages>
        <search-modal v-if="Store.contentRouter == 'search'"></search-modal>

        <div class="v-content" id="v-content" v-show="Store.contentRouter == 'content'" @scroll="scrolled">
            @yield('content')
        </div>

        <right-sidebar v-show="showRightSidebar"></right-sidebar>
    </div>


    {{--<report-submission v-if="modalRouter == 'report-submission'" :submission="reportSubmissionId" :category="reportCategory"></report-submission>--}}
    {{--<report-comment v-if="modalRouter == 'report-comment'" :comment="reportCommentId" :category="reportCategory"></report-comment>--}}
    <rules v-if="modalRouter == 'rules'"></rules>
    <moderators v-if="modalRouter == 'moderators'"></moderators>
    <keyboard-shortcuts-guide v-if="modalRouter == 'keyboard-shortcuts-guide'"></keyboard-shortcuts-guide>
    <markdown-guide v-if="modalRouter == 'markdown-guide'"></markdown-guide>
    <crop-modal v-if="modalRouter == 'crop-user'" :type="'user'"></crop-modal>
    <crop-modal v-if="modalRouter == 'crop-category'" :type="'category'"></crop-modal>
</div>

@include('php-to-js-data')

@yield('script')
	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
	<script src="{{ mix('/js/app.js') }}"></script>
@yield('footer')

</body>
</html>
