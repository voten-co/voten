<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('head')
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('env-to-js-data')

    <link rel="shortcut icon" href="{{ config('app.favicon') }}">
</head>

<body>

@include('google-analytics')

<div id="voten-app">
    <vue-progress-bar></vue-progress-bar>

    <div class="shade" v-if="showTour"></div>
    <tour v-if="showTour && activeTour.id == 'os-notifications'" :position="{ top: '7em', left: '39%' }"></tour>

    <div class="v-content-wrapper">
        <left-sidebar v-show="showSidebars"></left-sidebar>

        <div class="v-content" id="v-content" @scroll.passive="scrolled">
            <announcement></announcement>
            
            @yield('content')
        </div>

        <right-sidebar v-show="showSidebars"></right-sidebar>
    </div>

    <messages v-show="Store.modals.messages.show"></messages>
    <search-modal v-if="Store.modals.search.show"></search-modal>    
    <sidebar-settings :visible.sync="Store.modals.sidebarSettings.show" v-if="Store.modals.sidebarSettings.show"></sidebar-settings>
    <feed-settings :visible.sync="Store.modals.feedSettings.show" v-if="Store.modals.feedSettings.show && isLoggedIn"></feed-settings>
    <report-comment :visible.sync="Store.modals.reportComment.show" v-if="Store.modals.reportComment.show"></report-comment>
    <report-submission :visible.sync="Store.modals.reportSubmission.show" v-if="Store.modals.reportSubmission.show"></report-submission>
    <notifications :visible.sync="Store.modals.notifications.show" v-show="Store.modals.notifications.show"></notifications>
    <new-submission v-show="Store.modals.newSubmission.show" :visible.sync="Store.modals.newSubmission.show"></new-submission>
    <preferences v-if="Store.modals.preferences.show" :visible.sync="Store.modals.preferences.show"></preferences>
    <new-channel v-show="Store.modals.newChannel.show" :visible.sync="Store.modals.newChannel.show"></new-channel>
    <markdown-guide v-if="Store.modals.markdownGuide.show" :visible.sync="Store.modals.markdownGuide.show"></markdown-guide>
    <feedback v-if="Store.modals.feedback.show" :visible.sync="Store.modals.feedback.show"></feedback>
    <photo-viewer v-if="Store.modals.photoViewer.show" :visible.sync="Store.modals.photoViewer.show"></photo-viewer>
    <gif-player v-if="Store.modals.gifPlayer.show" :visible.sync="Store.modals.gifPlayer.show"></gif-player>
    <embed-viewer v-if="Store.modals.embedViewer.show" :visible.sync="Store.modals.embedViewer.show"></embed-viewer>
    <keyboard-shortcuts-guide v-if="Store.modals.keyboardShortcutsGuide.show" :visible.sync="Store.modals.keyboardShortcutsGuide.show"></keyboard-shortcuts-guide>
    <mobile-visitor-warning v-if="Store.modals.mobileVisitorWarning.show" :visible.sync="Store.modals.mobileVisitorWarning.show"></mobile-visitor-warning>
    <ban-user-modal v-if="Store.modals.banUser.show" :visible.sync="Store.modals.banUser.show"></ban-user-modal>
</div>

@include('php-to-js-data')

@yield('script')
	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
	<script src="{{ mix('/js/app.js') }}"></script>
@yield('footer')

</body>
</html>
