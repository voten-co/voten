<template>
    <div class="col-7 padding-bottom-10">
		<div class="flex-space flex-align-center user-select margin-bottom-2 mobile-padding">
			<div>
				<ul class="flat-nav">
					<router-link tag="li" :to="{ path: '/' }" class="item" :class="{ 'active': sort == 'hot' }">
						Hot
					</router-link>

					<router-link tag="li" :to="{ path: '/?sort=new' }" class="item" :class="{ 'active': sort == 'new' }">
						New
					</router-link>

					<router-link tag="li" :to="{ path: '/?sort=rising' }" class="item" :class="{ 'active': sort == 'rising' }">
						Rising
					</router-link>
				</ul>
			</div>

			<div>
				<div class="ui icon top right active-blue pointing dropdown feed-panel-button" @click="mustBeLogin">
					<i class="v-icon v-config"></i>

					<div class="menu">
						<button class="item" @click="changeFilter('subscribed-channels')" :class="{ 'active' : filter == 'subscribed-channels' }">
							Subscribed channels
						</button>

						<button class="item" @click="changeFilter('all-channels')" :class="{ 'active' : filter == 'all-channels' }">
							All channels
						</button>

						<button class="item" @click="changeFilter('moderating-channels')" :class="{ 'active' : filter == 'moderating-channels' }" v-if="isModerating">
							Moderating channels
						</button>

						<button class="item" @click="changeFilter('bookmarked-channels')" :class="{ 'active' : filter == 'bookmarked-channels' }">
							Bookmarked channels
						</button>

						<button class="item" @click="changeFilter('by-bookmarked-users')" :class="{ 'active' : filter == 'by-bookmarked-users' }">
							By bookmarked users
						</button>
					</div>
				</div>

				<button class="feed-panel-button btn-nth--h" @click="refresh"
				data-toggle="tooltip" data-placement="bottom" title="Refresh">
					<i class="v-icon v-refetch"></i>
				</button>
			</div>
		</div>

		<home-submissions></home-submissions>
    </div>
</template>

<script>
	import HomeSubmissions from '../components/HomeSubmissions.vue';
	import Helpers from '../mixins/Helpers';
	import LocalStorage from '../mixins/LocalStorage';

    export default {
    	mixins: [Helpers, LocalStorage],

	    components: {
	        HomeSubmissions
	    },

        created() {
            this.setPageTitle('Voten - Social Bookmarking For The 21st Century', true);
            this.askNotificationPermission();
        },

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip();
	        	this.$root.loadSemanticDropdown();
			})
		},

        computed: {
        	filter() {
        	    return Store.feedFilter;
        	},

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
        	 * changes the filter for home feed
        	 *
        	 * @return void
        	 */
        	changeFilter(filter) {
        		if (Store.feedFilter == filter) return;

        	    Store.feedFilter = filter;

        	    this.putLS('feed-filter', filter);

        	    this.refresh();
        	},

        	/**
        	 * fires the refresh event
        	 *
        	 * @return void
        	 */
        	refresh() {
        	    this.$eventHub.$emit('refresh-home');
        	},

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
