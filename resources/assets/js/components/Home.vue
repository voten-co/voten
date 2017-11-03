<template>
	<div class="home-wrapper" id="home">
		<nav class="nav has-shadow user-select">
			<div class="container">
				<h1 class="title">
					Home
				</h1>

				<div class="nav-left">
					<router-link :to="{ path: '/' }" class="nav-item is-tab" :class="{ 'is-active': sort == 'hot' }">
						Hot
					</router-link>

					<router-link :to="{ path: '/?sort=new' }" class="nav-item is-tab" :class="{ 'is-active': sort == 'new' }">
						New
					</router-link>

					<router-link :to="{ path: '/?sort=rising' }" class="nav-item is-tab" :class="{ 'is-active': sort == 'rising' }">
						Rising
					</router-link>
				</div>

				<div class="flex-center">
					<!--<div class="ui icon top right active-blue pointing dropdown feed-panel-button" @click="mustBeLogin" v-tooltip.bottom="{content: 'Customize'}">-->
						<!--<i class="v-icon v-sliders"></i>-->

						<!--<div class="menu">-->
							<!--<div class="header">-->
								<!--Filter by-->
							<!--</div>-->

							<!--<button class="item" @click="changeFilter('subscribed-channels')" :class="{ 'active' : filter == 'subscribed-channels' }">-->
								<!--Subscribed channels-->
							<!--</button>-->

							<!--<button class="item" @click="changeFilter('all-channels')" :class="{ 'active' : filter == 'all-channels' }">-->
								<!--All channels-->
							<!--</button>-->

							<!--<button class="item" @click="changeFilter('moderating-channels')" :class="{ 'active' : filter == 'moderating-channels' }"-->
							 <!--v-if="isModerating">-->
								<!--Moderating channels-->
							<!--</button>-->

							<!--<button class="item" @click="changeFilter('bookmarked-channels')" :class="{ 'active' : filter == 'bookmarked-channels' }">-->
								<!--Bookmarked channels-->
							<!--</button>-->

							<!--<button class="item" @click="changeFilter('by-bookmarked-users')" :class="{ 'active' : filter == 'by-bookmarked-users' }">-->
								<!--By bookmarked users-->
							<!--</button>-->
						<!--</div>-->
					<!--</div>-->

					<el-tooltip content="Refresh (R)" placement="bottom" transition="false" :open-delay="100">
						<button class="feed-panel-button" @click="refresh">
							<i class="v-icon v-refetch"></i>
						</button>
					</el-tooltip>

					<el-tooltip content="Customize Feed" placement="bottom" transition="false" :open-delay="100">
						<button class="feed-panel-button margin-right-half" @click="dialogTableVisible = true">
							<i class="v-icon v-sliders"></i>
						</button>
					</el-tooltip>

					<el-button type="primary" icon="el-icon-plus" plain size="medium" @click="$router.push('/submit')">
						Submit
					</el-button>
				</div>
			</div>
		</nav>
		
		<!-- <announcement></announcement> -->
		<!-- <el-alert
			title="success alert"
			type="success">
		</el-alert> -->

		<home-submissions></home-submissions>
		
		<scroll-button scrollable="submissions"></scroll-button>

		<el-dialog
				title="Feed Filters"
				:visible.sync="dialogTableVisible"
				:width="'35'"
		>
			<p>
				Other than subscribing to channels, there are more filters available to make sure you get the
				content that suits you the best.
			</p>

			<el-form label-position="top" label-width="10px" :model="form">
				<div class="form-toggle">
					<span>Display NSFW submissions: <small>(You must be 18 or older)</small></span>
					<el-switch v-model="form.nsfw"></el-switch>
				</div>

				<div class="form-toggle">
					<span>Display preview for NSFW submissions:</span>
					<el-switch v-model="form.nsfwMedia"></el-switch>
				</div>

				<div class="form-toggle">
					<span>Exclude my upvoted submissions:</span>
					<el-switch v-model="form.exclude_upvoted_submissions"></el-switch>
				</div>

				<div class="form-toggle no-border">
					<span>Exclude my downvoted submissions:</span>
					<el-switch v-model="form.exclude_downvoted_submissions"></el-switch>
				</div>

				<!-- submit -->
				<el-form-item v-if="changed">
					<el-button type="success" @click="save" :loading="sending" size="medium">Save</el-button>
				</el-form-item>
			</el-form>
		</el-dialog>
	</div>
</template>

<script>
	import HomeSubmissions from '../components/HomeSubmissions.vue';
	import Announcement from '../components/Announcement.vue';
	import Helpers from '../mixins/Helpers';
	import LocalStorage from '../mixins/LocalStorage';
	import ScrollButton from '../components/ScrollButton.vue';

    export default {
    	mixins: [Helpers, LocalStorage],

	    components: {
	        HomeSubmissions,
	        Announcement, 
			ScrollButton
		},

        data() {
    	    return {
                gridData: [{
                    date: '2016-05-02',
                    name: 'John Smith',
                    address: 'No.1518,  Jinshajiang Road, Putuo District'
                }, {
                    date: '2016-05-04',
                    name: 'John Smith',
                    address: 'No.1518,  Jinshajiang Road, Putuo District'
                }, {
                    date: '2016-05-01',
                    name: 'John Smith',
                    address: 'No.1518,  Jinshajiang Road, Putuo District'
                }, {
                    date: '2016-05-03',
                    name: 'John Smith',
                    address: 'No.1518,  Jinshajiang Road, Putuo District'
                }],
                dialogTableVisible: false,
			}
		},

        created() {
            this.setPageTitle('Voten - Social Bookmarking For The 21st Century', true);
            this.askNotificationPermission();
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
