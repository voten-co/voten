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

					<router-link active-class="is-active" :to="{name: 'bookmarked-categories'}" class="nav-item is-tab">
						Channels
					</router-link>

					<router-link active-class="is-active" :to="{name: 'bookmarked-users'}" class="nav-item is-tab">
						Users
					</router-link>
				</div>
			</div>
		</nav>
		
		<keep-alive>
			<router-view></router-view>
		</keep-alive>
	</div>
</template>


<script>
	export default {
		beforeRouteLeave(to, from, next) {
			this.$destroy();

			next();
		}
	}
</script>
