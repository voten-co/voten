<template>
	<nav class="nav has-shadow user-select admin-panel-header">
		<div class="container">
			<div class="nav-left">
				<router-link :to="'/big-daddy/help'" class="nav-item is-tab" active-class="is-active">
					Help
				</router-link>

				<router-link :to="'/big-daddy/reports/submissions'" class="nav-item is-tab" active-class="is-active">
					Reported Submissions
				</router-link>

				<router-link :to="'/big-daddy/reports/comments'" class="nav-item is-tab" active-class="is-active">
					Reported Comments
				</router-link>

				<router-link :to="{name: 'admin-panel-all-users'}" class="nav-item is-tab" active-class="is-active">
					Users
				</router-link>

				<router-link :to="'/big-daddy/channels'" class="nav-item is-tab" active-class="is-active">
					Channels
				</router-link>

				<router-link :to="'/big-daddy/submissions'" class="nav-item is-tab" active-class="is-active">
					Submissions
				</router-link>

				<router-link :to="'/big-daddy/comments'" class="nav-item is-tab" active-class="is-active">
					Comments
				</router-link>

				<router-link :to="'/big-daddy/suggested-categories'" class="nav-item is-tab" active-class="is-active">
					Suggesteds
				</router-link>
			</div>
		</div>
	</nav>
</template>
