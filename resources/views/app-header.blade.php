<header class="header-voten user-select">
	<div class="left-header">
		<vui-menu-button :checked="sidebar"></vui-menu-button>
	</div>

    <router-link :to="{ path: '/' }" class="desktop-only">
        <img src="/imgs/voten-logo.png" alt="{{ config('app.name') }}" @click="closeModals"
        	class="logo-voten" data-toggle="tooltip" data-placement="bottom" title="Home">
    </router-link>

    <div class="flex-display">
        <div class="dropdown head-notification-icons">
        	@if(Auth::check())
				<button type="button" class="btn-nth relative" id="messages-btn" @click="changeRoute('messages')"
	            data-toggle="tooltip" data-placement="bottom" title="Messages">
	                <i class="v-icon v-inbox-1" aria-hidden="true"></i>
	                <span class="queue-number" v-show="unreadMessages" v-text="unreadMessages"></span>
	            </button>

	            <button type="button" class="btn-nth relative" aria-haspopup="true"
				data-toggle="tooltip" data-placement="bottom" title="Notifications" aria-expanded="false" @click="changeRoute('notifications')">
	           		<i class="v-icon v-bell-2" aria-hidden="true"></i>
	               	<span class="queue-number" v-show="unreadNotifications" v-text="unreadNotifications"></span>
	            </button>
        	@endif

        	@if (!Auth::check())
        		<button class="v-button v-button--green relative" @click="mustBeLogin">
	        		Sign up/Log in
	        	</button>
        	@endif

            <button type="button" class="btn-nth relative" aria-haspopup="true"
			data-toggle="tooltip" data-placement="bottom" title="Search" aria-expanded="false" @click="changeRoute('search')">
           		<i class="v-icon v-search-2" aria-hidden="true"></i>
            </button>

			<router-link :to="'/'" class="btn-nth relative" aria-haspopup="true"
			data-toggle="tooltip" data-placement="bottom" title="Home" aria-expanded="false">
           		<i class="v-icon v-home" aria-hidden="true" @click="closeModals"></i>
            </router-link>
        </div>

		@if(Auth::check())
	        <div class="ui icon top right pointing dropdown pull-right">
	            <img src="{{Auth::user()->avatar}}" alt="{{Auth::user()->username}}" class="header-avatar">

	            <div class="menu">
	                <div class="header">My {{ config('app.name') }}</div>

					<router-link :to="'/submit'" class="item">
	                    <i class="v-icon v-submit" aria-hidden="true"></i>
	                    Submit
	                </router-link>

	                <router-link :to="{ path: '/bookmarks' }" class="item">
	                    <i class="v-icon v-unbookmark go-yellow" aria-hidden="true"></i>
	                    Bookmarks
	                </router-link>

	    			<router-link :to="'{{ '/@' . Auth::user()->username }}/settings'" class="item">
	                    <i class="v-icon v-tools go-primary" aria-hidden="true"></i>
	                    Settings
	                </router-link>

					<router-link :to="'/channel'" class="item">
	                    <i class="v-icon v-hash go-green" aria-hidden="true"></i>
	                    New Channel
	                </router-link>

	                <div class="ui divider"></div>

					<div class="header" v-if="Store.moderatingCategories.length">Moderating Channels</div>
					<router-link :to="'/c/' + item.name" class="item" v-for="(item, index) in Store.moderatingCategories"
					:key="item.id" v-if="Store.moderatingCategories.length && index < 6">
						<img class="square" :src="item.avatar" :alt="item.name">
						@{{ item.name }}
					</router-link>
					<div class="ui divider" v-if="Store.moderatingCategories.length && Store.moderatingCategories.length < 6"></div>

					<div class="item" v-if="Store.moderatingCategories.length && Store.moderatingCategories.length > 6">
						<i class="v-icon v-more"></i>
						<span class="text">More</span>
						<div class="left menu">
							<router-link :to="'/c/' + item.name" class="item" v-for="(item, index) in Store.moderatingCategories"
							:key="item.id" v-if="index > 6">
								<img class="square" :src="item.avatar" :alt="item.name">
								@{{ item.name }}
							</router-link>
						</div>
					</div>

	                @if( Auth::user()->isVotenAdministrator() )
		                <div class="header">{{ config('app.name') }} Administrators</div>

		                <router-link :to="'/big-daddy'" class="item">
		                    <i class="v-icon v-linux" aria-hidden="true"></i>
		                    Big Daddy
		                </router-link>

		                <div class="ui divider"></div>
	                @endif

	    			<router-link :to="'/find-channels'" class="item">
	                    <i class="v-icon v-book go-primary" aria-hidden="true"></i>
	                    Find #Channels
	                </router-link>

	                <a class="item desktop-only" @click="changeModalRoute('keyboard-shortcuts-guide')">
	                	<i class="v-icon v-keyboard" aria-hidden="true"></i>
	                    Keyboard Shortcuts
	                </a>

	                <router-link :to="'/help'" class="item">
	                    <i class="v-icon v-help go-red" aria-hidden="true"></i>
	                    Help
	                </router-link>

					<div class="item">
						<i class="v-icon v-left-hand"></i>
						<span class="text">{{ config('app.name') }}</span>
						<div class="left menu green">
							<a href="/about" class="item">
			                    About
			                </a>

			                <a href="mailto:{{ config('settings.info_email') }}" class="item">
			                    Contact Us
			                </a>

			                <a class="item" @click="changeModalRoute('feedback')">
			                    Feedback
			                </a>

			                <a class="item" href="/tos">
			                    Site Rules
			                </a>

			                <a class="item" href="/privacy-policy">
			                    Privacy Policy
			                </a>

							<a href="/credits" class="item">
			                    Credits
			                </a>
						</div>
					</div>

	                <a href="/logout" class="item">
	                    <i class="v-icon v-logout go-green" aria-hidden="true"></i>
	                    Logout
	                </a>
	            </div>
	        </div>
		@endif
    </div>
</header>
