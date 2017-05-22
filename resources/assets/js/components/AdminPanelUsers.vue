<template>
    <div class="container margin-top-1 col-7">
		<div class="tabs is-fullwidth">
			<ul>
				<router-link tag="li" active-class="is-active" :to="{name: 'admin-panel-all-users'}">
					<a>
						Latest Registered
					</a>
				</router-link>

				<router-link tag="li" active-class="is-active" :to="{name: 'admin-panel-ban-users'}">
					<a>
						Ban Users
					</a>
				</router-link>
			</ul>
		</div>

		<router-view></router-view>
	</div>
</template>
