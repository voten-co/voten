<template>
    <div class="col-7 padding-bottom-10">
		<div class="tabs is-fullwidth">
			<ul>
				<router-link tag="li" active-class="is-active" :to="{ name: 'home-hot' }">
					<a>
						Hot
					</a>
				</router-link>

				<router-link tag="li" active-class="is-active" :to="{ name: 'home-new' }">
					<a>
						New
					</a>
				</router-link>

				<router-link tag="li" active-class="is-active" :to="{ name: 'home-rising' }">
					<a>
						Rising
					</a>
				</router-link>
			</ul>
		</div>

		<router-view></router-view>
    </div>
</template>

<script>
    export default {

        created: function() {
            this.askNotificationPermission()
        },

        methods: {
        	/**
        	 * In case the user has just joined to the Voten community let's ask them for the awesome Desktop notifications permission.
        	 *
        	 * @return void
        	 */
        	 askNotificationPermission () {
                 if (this.$route.query.newbie == 1) {
                     if ('Notification' in window) {
                         Notification.requestPermission()
                     } else {
                         console.log('Your browser does not support desktop notifications. ')
                     }
                 }
        	}
        },
    }
</script>
