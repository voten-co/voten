<template>
	<div class="container margin-top-1 col-7">
		<div class="tabs is-fullwidth">
			<ul>
				<router-link tag="li" active-class="is-active" :to="{name: 'bookmarked-submissions'}">
					<a>
						Submissions
					</a>
				</router-link>

				<router-link tag="li" active-class="is-active" :to="{name: 'bookmarked-comments'}">
					<a>
						Comments
					</a>
				</router-link>

				<router-link tag="li" active-class="is-active" :to="{name: 'bookmarked-categories'}">
					<a>
						Channels
					</a>
				</router-link>

				<router-link tag="li" active-class="is-active" :to="{name: 'bookmarked-users'}">
					<a>
						Users
					</a>
				</router-link>
			</ul>
		</div>

		<router-view></router-view>
	</div>
</template>