<template>
	<div class="home-wrapper" id="bookmarks">
		<nav class="nav has-shadow user-select">
			<div class="container">
				<h1 class="title">
					My Bookmarks
				</h1>
				
				<div class="nav-left">
					<router-link active-class="is-active" :to="{name: 'bookmarked-submissions'}" class="nav-item is-tab">
						Submissions
					</router-link>

					<router-link active-class="is-active" :to="{name: 'bookmarked-comments'}" class="nav-item is-tab">
						Comments
					</router-link>

					<router-link active-class="is-active" :to="{name: 'bookmarked-channels'}" class="nav-item is-tab">
						Channels
					</router-link>

					<router-link active-class="is-active" :to="{name: 'bookmarked-users'}" class="nav-item is-tab">
						Users
					</router-link>
				</div>
			</div>
		</nav>

		<router-view></router-view>
	</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    beforeRouteEnter(to, from, next) {
        Store.page.bookmarkedSubmissions.clear();
        Store.page.bookmarkedComments.clear();
        Store.page.bookmarkedChannels.clear();
        Store.page.bookmarkedUsers.clear();

        next();
    }
};
</script>
