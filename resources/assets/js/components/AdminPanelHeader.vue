<template>
	<nav class="nav has-shadow user-select admin-panel-header">
		<div class="container">
			<div class="nav-left">
				<router-link :to="'/big-daddy/'" class="nav-item is-tab" active-class="is-active" exact>
					Dashboard 
				</router-link>

				<router-link :to="'/big-daddy/submissions'" class="nav-item is-tab" active-class="is-active">
					Submissions
				</router-link>

				<router-link :to="'/big-daddy/comments'" class="nav-item is-tab" active-class="is-active">
					Comments
				</router-link>

				<router-link :to="'/big-daddy/suggested-channels'" class="nav-item is-tab" active-class="is-active">
					Suggesteds
				</router-link>
			</div>
		</div>
	</nav>
</template>
