
<template>
	<div class="sidebar-left user-select" :class="showTour && activeTour.id === 'left-sidebar' ? 'shade-item relative' : 'overflow-hidden'">
		<tour v-if="showTour && activeTour.id == 'left-sidebar'" :position="{ top: '15%', left: '6em' }"></tour>
		
		<div class="top">
			<!-- Home -->
			<el-tooltip content="Home Feed (H)"
						placement="right"
						transition="false"
						:open-delay="500">
				<a @click.prevent="goHome"
				href="/"
				:class="{ 'active': activeRoute === 'home' }"
				class="item">
					<i class="v-icon v-home"
					aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<!-- Profile -->
			<el-tooltip content="Profile (P)"
						placement="right"
						transition="false"
						v-if="isLoggedIn"
						:open-delay="500">
				<a @click.prevent="pushRouter('/@' + auth.username)"
				:href="'/@' + auth.username"
				:class="{ 'active': activeRoute === 'profile' }"
				class="item">
					<i class="v-icon v-profile"
					aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<!-- Notifications -->
			<el-tooltip content="Notifications (N)"
						placement="right"
						transition="false"
						:open-delay="500"
						v-if="isLoggedIn">
				<a class="item"
				:class="{'active' : activeRoute === 'notifications'}"
				@click="Store.modals.notifications.show = true">
					<el-badge :value="unreadNotifications"
							:max="99">
						<i class="el-icon-bell"
						aria-hidden="true"></i>
					</el-badge>
				</a>
			</el-tooltip>

			<!-- Messages Inbox -->
			<el-tooltip content="Messages (M)"
						placement="right"
						transition="false"
						:open-delay="500"
						v-if="isLoggedIn">
				<a class="item"
					id="messages-btn"
					:class="{'active' : activeRoute === 'messages'}"
					@click="Store.modals.messages.show = true"
				>
					<el-badge :value="unreadMessages"
							:max="99">
						<i class="v-icon v-inbox"
						aria-hidden="true"></i>
					</el-badge>
				</a>
			</el-tooltip>

			<!-- Bookmarks -->
			<el-tooltip content="Bookmarks (B)"
						placement="right"
						transition="false"
						:open-delay="500"
						v-if="isLoggedIn">
				<a @click.prevent="pushRouter('/bookmarks')"
				href="/bookmarks"
				class="item"
				:class="{'active' : activeRoute === 'bookmarks'}">
					<i class="v-icon v-bookmark"
					aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<!-- Search -->
			<el-tooltip content="Search (/)"
						placement="right"
						transition="false"
						:open-delay="500">
				<a class="item"
				@click="Store.modals.search.show = true"
				:class="{'active' : activeRoute === 'search'}">
					<i class="el-icon-search"
					aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<!-- Settings -->
			<el-tooltip content="Preferences"
						placement="right"
						transition="false"
						:open-delay="500"
						v-if="isLoggedIn">
				<a class="item"
				@click.prevent="Store.modals.preferences.show = true"
				:class="{'active' : activeRoute === 'settings'}">
					<i class="el-icon-setting"
					aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<!-- Submit -->
			<el-tooltip content="Add Content"
						placement="right"
						transition="false"
						:open-delay="500"
						v-if="isLoggedIn">
				<a class="item"
				@click="$eventHub.$emit('submit')"
				:class="{'active' : activeRoute === 'submit'}">
					<i class="el-icon-plus"
					aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<!-- Help Center for guests -->
			<el-tooltip content="Help Center"
						placement="right"
						transition="false"
						:open-delay="500"
						v-if="isGuest">
				<a class="item"
				href="https://help.voten.co/"
				target="_blank">
					<i class="el-icon-question"
					aria-hidden="true"></i>
				</a>
			</el-tooltip>
		</div>

		<div class="bottom"
		     v-if="isLoggedIn">
			<!-- admin buttons -->
			<el-tooltip content="Backend Dashboard"
			            placement="right"
			            transition="false"
			            :open-delay="500"
			            v-if="meta.isVotenAdministrator">
				<a class="item"
				   href="/backend"
				   target="_blank">
					<i class="el-icon-service"
					   aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<el-tooltip content="Big-daddy Dashboard"
			            placement="right"
			            transition="false"
			            :open-delay="500"
			            v-if="meta.isVotenAdministrator">
				<a class="item"
				   @click.prevent="pushRouter('/big-daddy')"
				   href="/big-daddy">
					<i class="el-icon-view"
					   aria-hidden="true"></i>
				</a>
			</el-tooltip>

			<!-- Help Center -->
			<el-tooltip content="Help Center"
			            placement="right"
			            transition="false"
			            :open-delay="500">
				<a class="item"
				   href="https://help.voten.co/"
				   target="_blank">
					<i class="el-icon-question"
					   aria-hidden="true"></i>
				</a>
			</el-tooltip>
		</div>
	</div>
</template>

<script>
import Helpers from '../../mixins/Helpers';
import Tour from '../../components/Tour';

export default {
    mixins: [Helpers],

    components: { Tour },

    computed: {
        submitURL() {
            return this.$route.params.name
                ? '/submit?channel=' + this.$route.params.name
                : '/submit';
        }
    },

    computed: {
        activeRoute() {
            if (this.$route.name === 'home') {
                return 'home';
            }

            if (
                this.$route.name === 'bookmarked-submissions' ||
                this.$route.name === 'bookmarked-comments' ||
                this.$route.name === 'bookmarked-users' ||
                this.$route.name === 'bookmarked-channels'
            ) {
                return 'bookmarks';
            }

            if (this.$route.params.username == auth.username) {
                return 'profile';
            }

            if (this.$route.name === 'submit') {
                return 'submit';
            }
        },

        unreadNotifications() {
            return Store.state.notifications.filter(
                (item) => item.read_at == null
            ).length;
        },

        unreadMessages() {
            return Store.state.contacts.filter(
                (item) =>
                    item.last_message.author.id != auth.id &&
                    item.last_message.read_at == null
            ).length;
        }
    },

    methods: {
        pushRouter(route) {
            this.$eventHub.$emit('close');

            this.$router.push(route);
        },

        goHome() {
            if (this.$route.name != 'home') {
                Store.page.home.clear();
            }

            this.pushRouter('/');
        }
    }
};
</script>
