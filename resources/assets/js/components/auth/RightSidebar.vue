<template>
	<div class="sidebar-right user-select"
	     :class="theme">
		<div class="side-menu-wrapper"
		     :class="{'shade-item relative': showTour && activeTour.id === 'right-sidebar-menu'}">
			<tour v-if="showTour && activeTour.id == 'right-sidebar-menu'"
			      :position="{ top: '7em', right: '21em' }"></tour>

			<div class="box"
			     @click="showMenu =! showMenu">
				<span>
					<img :src="auth.avatar"
					     :alt="auth.username"
					     class="avatar">

					<strong class="break-words">{{ '@' + auth.username }}</strong>
				</span>

				<i :class="showMenu ? 'el-icon-close' : 'el-icon-arrow-down'"></i>
			</div>

			<el-collapse-transition>
				<ul v-show="showMenu"
				    class="menu">
					<li>
						<router-link class="item"
						             to="/">Home</router-link>
					</li>
					<li>
						<router-link class="item"
						             :to="'/@' + auth.username">Profile</router-link>
					</li>
					<li>
						<router-link class="item"
						             to="/bookmarks">Bookmarks</router-link>
					</li>
					<li>
						<router-link class="item"
						             :to="{name: 'subscriptions'}">Subscriptions</router-link>
					</li>
					<li class="item"
					    @click="Store.modals.preferences.show = true">Preferences</li>

					<hr>

					<li>
						<router-link :to="{name: 'discover-channels'}"
						             class="item">Discover channels</router-link>
					</li>
					<li class="item"
					    @click="Store.modals.newChannel.show = true">Create a new channel</li>
					<li class="item"
					    @click="Store.modals.keyboardShortcutsGuide.show = true">Keyboard shortcuts</li>
					<li>
						<a href="https://help.voten.co/"
						   target="_blank"
						   class="item">Help center</a>
					</li>

					<el-collapse-transition>
						<div v-show="showSubMenu">
							<li>
								<router-link to="/about"
								             class="item">About</router-link>
							</li>
							<li>
								<a href="mailto:info@voten.co"
								   class="item">Contact Us</a>
							</li>
							<li>
								<router-link to="/tos"
								             class="item">Terms of service</router-link>
							</li>
							<li>
								<router-link to="/privacy-policy"
								             class="item">Privacy policy</router-link>
							</li>
							<li>
								<a href="https://medium.com/voten"
								   class="item"
								   target="_blank">Blog</a>
							</li>
							<li>
								<router-link to="/credits"
								             class="item">Credits</router-link>
							</li>
							<li>
								<a href="https://github.com/voten-co/voten"
								   class="item"
								   target="_blank">Source code</a>
							</li>
						</div>
					</el-collapse-transition>

					<div class="align-center">
						<el-button type="text"
						           @click="showSubMenu =! showSubMenu"
						           class="go-gray full-width"
						           size="mini">
							{{ showSubMenu ? 'Show less' : 'Show more' }}
						</el-button>
					</div>

					<hr>

					<li class="item go-green"
					    @click="Store.modals.feedback.show = true">Give feedback</li>
					<li class="item go-red"
					    @click="signOut">Sign Out</li>
				</ul>
			</el-collapse-transition>
		</div>

		<div class="fixed-header"
		     :class="{'shade-item relative': showTour && activeTour.id === 'right-sidebar-channels'}">
			<tour v-if="showTour && activeTour.id == 'right-sidebar-channels'"
			      :position="{ top: '14em', right: '21em' }"></tour>

			<div class="flex-space">
				<div class="menu-label">
					<span>
						<strong>My Channels:</strong>
						<span v-if="channelsCount">({{ channelsCount }})</span>
					</span>

					<el-tooltip content="Customize Sidebar"
					            placement="left"
					            transition="false"
					            :open-delay="500">
						<i class="el-icon-setting darker-on-hover"
						   @click="Store.modals.sidebarSettings.show = true"></i>
					</el-tooltip>
				</div>
			</div>

			<el-input :placeholder="filterForHumans + ' (' + channelsCount + ')'"
			          prefix-icon="el-icon-search"
			          size="small"
			          v-model="subscribedFilter"
			          class="search margin-bottom-1"
			          clearable
			          name="subscribedFilter"
			          autocorrect="off"
			          autocapitalize="off"
			          spellcheck="false"></el-input>
		</div>

		<aside class="menu"
		       :class="{'shade-item relative': activeTour.id === 'right-sidebar-channels'}">
			<div class="no-subscription"
			     v-if="!sortedSubscribeds.length">
				<i class="v-icon v-channel"
				   aria-hidden="true"></i> No channels to display
			</div>

			<ul class="menu-list"
			    v-else>
				<li v-for="channel in sortedSubscribeds"
				    :key="channel.id">
					<router-link :to="'/c/' + channel.name"
					             active-class="active">
						<img class="square"
						     :src="channel.avatar"
						     :alt="channel.name"
						     v-if="showChannelAvatars">
						<span v-else>#</span>
						<span class="v-channels-text">{{ channel.name }}</span>
					</router-link>
				</li>
			</ul>

			<div class="align-center">
				<el-button type="text"
				           @click="moreChannels"
				           class="go-gray full-width"
				           size="mini"
				           v-if="showLoadMoreChannels">
					Show more
				</el-button>

				<el-button type="text"
				           @click="pushToDiscoverChannels"
				           class="go-gray full-width"
				           size="mini"
				           v-if="showDiscoverChannels">
					Discover channels
				</el-button>
			</div>
		</aside>
	</div>
</template>

<script>
import Helpers from '../../mixins/Helpers';
import Tour from '../../components/Tour';

export default {
    mixins: [Helpers],

    components: { Tour },

    data() {
        return {
            subscribedFilter: '',
            showMenu: false,
            showSubMenu: false
        };
    },

    watch: {
        $route: function() {
            this.subscribedFilter = '';
        },

        showMenu() {
            if (this.showMenu === false) {
                this.showSubMenu = false;
            }
        }
    },

    computed: {
        showLoadMoreChannels() {
            return this.channels.length > this.channelsLimit && !this.subscribedFilter;
        },

        showDiscoverChannels() {
            return (
                this.channels.length < this.channelsLimit &&
                !this.subscribedFilter &&
                Store.settings.rightSidebar.channelsFilter == 'subscribed'
            );
        },

        channels() {
            if (this.filter == 'bookmarked') {
                return Store.state.bookmarkedChannels;
            }

            if (this.filter == 'moderating') {
                return Store.state.moderatingChannels;
            }

            // subscribed
            return Store.state.subscribedChannels;
        },

        channelsCount() {
            return this.channels.length;
        },

        showChannelAvatars() {
            return Store.settings.rightSidebar.showChannelAvatars;
        },

        channelsLimit() {
            return Store.settings.rightSidebar.channelsLimit;
        },

        theme() {
            if (! this.showTour) {
                return 'theme-' + this.str_slug(Store.settings.rightSidebar.color) + ' overflow-hidden';
            }

            return 'theme-' + this.str_slug(Store.settings.rightSidebar.color);
        },

        filter() {
            return Store.settings.rightSidebar.channelsFilter;
        },

        filterForHumans() {
            if (this.filter == 'subscribed') return 'Subscribed Channels';

            if (this.filter == 'bookmarked') return 'Bookmarked Channels';

            if (this.filter == 'moderating') return 'Moderating Channels';
        },

        sortedSubscribeds() {
            let self = this;

            return _.orderBy(
                self.channels.filter(
                    channel => channel.name.toLowerCase().indexOf(self.subscribedFilter.toLowerCase()) !== -1
                ),
                'subscribers_count',
                'desc'
            ).slice(0, self.channelsLimit > 2 ? self.channelsLimit : 2);
        }
    },

    created() {
        this.$eventHub.$on('pressed-esc', this.closeMenu);
    },

    methods: {
        closeMenu() {
            this.showMenu = false;
        },

        moreChannels() {
            Store.settings.rightSidebar.channelsLimit += 15;
        },

        pushToDiscoverChannels() {
            this.$router.push({ name: 'discover-channels' });
        },

        lessChannels() {
            Store.settings.rightSidebar.channelsLimit -= 15;
        },

        signOut() {
            this.$confirm(`Are you sure about signing out of your account?`, 'Confirm', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Never mind',
                type: 'warning'
            })
                .then(() => {
                    Vue.clearLS();

                    axios
                        .post('/logout')
                        .then(() => {
                            window.location = '/';
                        })
                        .catch(() => {
                            this.$message({
                                message: 'Something went wrong.',
                                type: 'error'
                            });
                        });
                })
                .catch(() => {});
        }
    }
};
</script>
