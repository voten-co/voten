<template>
    <div class="side-fixed" id="v-sidebar">
		<div class="sidebar-left">
			<div class="item">
				<router-link :to="'/' + '@' + auth.username" :class="{ 'active': ($route.name == 'user-submissions' || $route.name == 'user-comments' || $route.name == 'user-upvotes' || $route.name == 'user-downvotes') }">
					<img :src="auth.avatar" :alt="auth.username" class="avatar">
				</router-link>
			</div>
			<div class="item">
				<router-link :to="'/'" :class="{ 'active': $route.name == 'home' }">
					<home-icon width="30" height="30"></home-icon>
				</router-link>
			</div>
			<div class="item">
				<router-link :to="{ path: '/bookmarks' }" active-class="active">
					<bookmark-icon width="30" height="30"></bookmark-icon>
				</router-link>
			</div>

			<div class="item">
				<router-link :to="{ path: submitURL }" active-class="active">
					<submit-icon width="30" height="30"></submit-icon>
				</router-link>
			</div>
			<div class="item">
				<router-link :to="{ path: '/@' + auth.username + '/settings' }" active-class="active">
					<settings-icon width="30" height="30"></settings-icon>
				</router-link>
			</div>
			<div class="item">
				<router-link :to="{ path: '/find-channels' }" active-class="active">
					<search-icon width="30" height="30"></search-icon>
				</router-link>
			</div>
			<div class="item">
				<router-link :to="{ path: '/channel' }" active-class="active">
					<channel-icon width="30" height="30"></channel-icon>
				</router-link>
			</div>
			<div class="item">
				<router-link :to="{ path: '/live' }" active-class="active">
					<chat-icon width="30" height="30"></chat-icon>
				</router-link>
			</div>
		</div>

		<div class="sidebar-right">
			<!--<div class="sidebar-avatar align-center">-->
			<!--<router-link :to="'/' + '@' + auth.username">-->
			<!--<img v-bind:src="auth.avatar" alt="My Voten"/>-->
			<!--</router-link>-->
			<!--</div>-->

			<!--<div class="profile-card">-->
			<!--<div class="profile-card-name">-->
			<!--<router-link :to="'/' + '@' + auth.username">-->
			<!--{{ '@' + auth.username}}-->
			<!--</router-link>-->
			<!--</div>-->
			<!--<div class="profile-card-status">{{ auth.name }}</div>-->
			<!--</div>-->

			<!--<hr>-->

			<!--<div class="flex-right">-->
			<!--<button class="v-button v-button-icon">-->
			<!--Hide-->
			<!--</button>-->
			<!--</div>-->

			<!--<div class="side-primary-menu">-->
				<!--<ul>-->
					<!--<li>-->
						<!--<router-link :to="'/'" :class="{ 'active': $route.name == 'home' }">-->
							<!--<home-icon width="30" height="30"></home-icon>-->
							<!--Home Feed-->
						<!--</router-link>-->
					<!--</li>-->

					<!--<li>-->
						<!--<router-link :to="'/' + '@' + auth.username" :class="{ 'active': ($route.name == 'user-submissions' || $route.name == 'user-comments' || $route.name == 'user-upvotes' || $route.name == 'user-downvotes') }">-->
							<!--<profile-icon width="30" height="30"></profile-icon>-->
							<!--Profile-->
						<!--</router-link>-->
					<!--</li>-->

					<!--<li>-->
						<!--<router-link :to="{ path: '/bookmarks' }" active-class="active">-->
							<!--<bookmark-icon width="30" height="30"></bookmark-icon>-->
							<!--Bookmarks-->
						<!--</router-link>-->
					<!--</li>-->

					<!--<li>-->
						<!--<router-link :to="{ path: submitURL }" active-class="active">-->
							<!--<submit-icon width="30" height="30"></submit-icon>-->
							<!--Submit-->
						<!--</router-link>-->
					<!--</li>-->

					<!--<li>-->
						<!--<router-link :to="{ path: '/@' + auth.username + '/settings' }" active-class="active">-->
							<!--<settings-icon width="30" height="30"></settings-icon>-->
							<!--Settings-->
						<!--</router-link>-->
					<!--</li>-->

					<!--<li>-->
						<!--<router-link :to="{ path: '/find-channels' }" active-class="active">-->
							<!--<search-icon width="30" height="30"></search-icon>-->
							<!--Find Channels-->
						<!--</router-link>-->
					<!--</li>-->

					<!--<li>-->
						<!--<router-link :to="{ path: '/channel' }" active-class="active">-->
							<!--<channel-icon width="30" height="30"></channel-icon>-->
							<!--Create Channel-->
						<!--</router-link>-->
					<!--</li>-->

					<!--<li>-->
						<!--<router-link :to="{ path: '/live' }" active-class="active">-->
							<!--<chat-icon width="30" height="30"></chat-icon>-->
							<!--Live-->
						<!--</router-link>-->
					<!--</li>-->
				<!--</ul>-->
			<!--</div>-->

			<!--<div class="v-side-quick-actions">-->
			<!---->

			<!---->

			<!---->
			<!--</div>-->


			<aside class="menu margin-top-1">
				<div class="flex-space">
					<p class="menu-label">
						Channels: <span v-if="Store.subscribedCategories.length">({{ Store.subscribedCategories.length }})</span>
					</p>

					<div class="ui icon top right active-blue pointing dropdown sidebar-panel-button">
						<i class="v-icon v-config"></i>

						<div class="menu">
							<div class="header">
								Limit to
							</div>
							<div class="ui left input">
								<input type="number" name="search" placeholder="Limit at..." min="2" spellcheck="false"
									   v-model="categoriesLimit">
							</div>

							<div class="header">
								Filter by
							</div>
							<button class="item" @click="changeFilter('subscribed-channels')" :class="{ 'active' : filter == 'subscribed-channels' }">
								Subscribed channels
							</button>

							<button class="item" @click="changeFilter('moderating-channels')" :class="{ 'active' : filter == 'moderating-channels' }" v-if="isModerating">
								Moderating channels
							</button>

							<button class="item" @click="changeFilter('bookmarked-channels')" :class="{ 'active' : filter == 'bookmarked-channels' }">
								Bookmarked channels
							</button>
						</div>
					</div>
				</div>

				<div class="ui category search side-box-search">
					<div class="ui mini icon input">
						<input class="prompt" type="text" placeholder="Channels..." v-model="subscribedFilter">
						<i class="v-icon v-search search icon"></i>
					</div>
				</div>

				<div class="no-subsciption" v-if="!Store.subscribedCategories.length && !Store.loading">
					<i class="v-icon v-sad" aria-hidden="true"></i>
					No channels to display
				</div>

				<ul class="menu-list" v-else>
					<li v-for="category in sortedSubscribeds">
						<router-link :to="'/c/' + category.name" active-class="active">
							<img class="square" v-bind:src="category.avatar" v-bind:alt="category.name">
							<span class="v-channels-text">{{ category.name }}</span>
						</router-link>
					</li>
				</ul>
			</aside>

			<!--<div class="sidebar-footer">-->
			<!--Add Content-->
			<!--</div>-->
		</div>
    </div>
</template>

<script>
import Helpers from '../mixins/Helpers';
import LocalStorage from '../mixins/LocalStorage';
import SubmitIcon from '../components/Icons/SubmitIcon.vue';
import BookmarkIcon from '../components/Icons/BookmarkIcon.vue';
import SettingsIcon from '../components/Icons/SettingsIcon.vue';
import SearchIcon from '../components/Icons/SearchIcon.vue';
import ChatIcon from '../components/Icons/ChatIcon.vue';
import ChannelIcon from '../components/Icons/ChannelIcon.vue';
import ProfileIcon from '../components/Icons/ProfileIcon.vue';
import HomeIcon from '../components/Icons/HomeIcon.vue';

export default {
	mixins: [Helpers, LocalStorage],

    components: {
        SubmitIcon,
        SettingsIcon,
        BookmarkIcon,
        SearchIcon,
        ChatIcon,
        ChannelIcon,
        ProfileIcon,
        HomeIcon
	},

    data: function () {
        return {
            subscribedFilter: '',
            categoriesLimit: 5,
        };
    },

	watch: {
		'$route': function () {
			this.subscribedFilter = ''
		},

		'categoriesLimit' : function () {
			this.putLS('sidebar-categories-limit', this.categoriesLimit);
		}
	},

	created() {
		if (this.isSetLS('sidebar-categories-limit')) {
			this.categoriesLimit = this.getLS('sidebar-categories-limit');
		}
	},

    computed: {
    	filter() {
    		return Store.sidebarFilter;
    	},
//
//    	categoriesTitle() {
//    		if (Store.sidebarFilter == "moderating-channels") {
//    			return "Moderating Channels";
//    		}
//
//    		if (Store.sidebarFilter == "bookmarked-channels") {
//    			return "Bookmarked Channels";
//    		}
//
//    		return "Subscribed Channels";
//    	},

        submitURL() {
            if (this.$route.params.name)
            	return "/submit?channel=" + this.$route.params.name

            return "/submit"
        },

    	/**
    	 * The sorted version of comments
    	 *
    	 * @return {Array} comments
    	 */
    	sortedSubscribeds() {
			var self = this

    		return _.orderBy(Store.subscribedCategories.filter(function (category) {
				return category.name.toLowerCase().indexOf(self.subscribedFilter.toLowerCase()) !== -1
			}), 'subscribers', 'desc').slice(0, (this.categoriesLimit > 2 ? this.categoriesLimit : 2))
    	},
    },

    methods: {
    	/**
         * navigates to home route. aaaand bit more in case the current route IS "home"
         *
         * @return void
         */
        homeRoute() {
            this.$eventHub.$emit('close');

            if (this.$route.name === 'home') {
            	this.$eventHub.$emit('refresh-home');
            }
        },

    	/**
    	 * changes the filter for sidebar
    	 *
    	 * @return void
    	 */
    	changeFilter(filter) {
    		if (Store.sidebarFilter == filter) return;

    	    Store.sidebarFilter = filter;

    	    this.putLS('sidebar-filter', filter);

    	    axios.get(this.authUrl('sidebar-categories'), {
    	    	params: {
    	    		sidebar_filter: Store.sidebarFilter
    	    	}
    	    }).then((response) => {
    	    	Store.subscribedCategories = response.data;
    	    });
    	},

        changeRoute: function(newRoute) {
        	this.$eventHub.$emit('new-route', newRoute)
        },
    },
}
</script>
