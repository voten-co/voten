<template>
    <div class="col-7 padding-bottom-10">
		<div class="tabs is-fullwidth">
			<ul>
				<router-link tag="li" :to="{ path: '/' }" :class="{ 'is-active': sort == 'hot' }">
					<a>
						Hot
					</a>
				</router-link>

				<router-link tag="li" :to="{ path: '/?sort=new' }" :class="{ 'is-active': sort == 'new' }">
					<a>
						New
					</a>
				</router-link>

				<router-link tag="li" :to="{ path: '/?sort=rising' }" :class="{ 'is-active': sort == 'rising' }">
					<a>
						Rising
					</a>
				</router-link>
			</ul>
		</div>

		<home-submissions></home-submissions>
    </div>
</template>

<script>
	import HomeSubmissions from '../components/HomeSubmissions.vue';
	import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

	    components: {
	        HomeSubmissions
	    },

        created() {
            this.setPageTitle('Voten - Social Bookmarking For The 21st Century', true);
            this.askNotificationPermission();
        },

        computed: {
        	/**
    	 	 * the sort of the page
	    	 *
	    	 * @return mixed
	    	 */
	    	sort() {
	    	    if (this.$route.query.sort == 'new')
	    	    	return 'new';

	    	    if (this.$route.query.sort == 'rising')
	    	    	return 'rising';

	    	    return 'hot';
	    	},
        },

        methods: {
        	/**
        	 * In case the user has just joined to the Voten community let's ask them for the awesome Desktop notifications permission.
        	 *
        	 * @return void
        	 */
        	 askNotificationPermission() {
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
