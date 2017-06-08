<template>
    <div class="side-fixed"  id="v-sidebar">
        <div class="sidebar-avatar align-center">
    		<router-link :to="'/' + '@' + auth.username">
                <img v-bind:src="auth.avatar" alt="My Voten"/>
            </router-link>
        </div>

        <div class="profile-card">
            <div class="profile-card-name">
    			<router-link :to="'/' + '@' + auth.username">
            		{{ '@' + auth.username}}
        		</router-link>
    		</div>
            <div class="profile-card-status">{{ auth.name }}</div>
        </div>

        <hr>

        <div class="v-side-quick-actions">
            <router-link :to="{ path: submitURL }">
                <i class="v-icon v-submit"
                    data-toggle="tooltip" data-placement="bottom" title="Submit"
                ></i>
            </router-link>

            <router-link :to="{ path: '/@' + auth.username + '/settings' }">
			    <i class="v-icon v-tools" aria-hidden="true"
			        data-toggle="tooltip" data-placement="bottom" title="Settings"
			    ></i>
			</router-link>

            <router-link :to="{ path: '/bookmarks' }">
                <i class="v-icon v-bookmark" aria-hidden="true"
                    data-toggle="tooltip" data-placement="bottom" title="Bookmarks"
                ></i>
            </router-link>

            <router-link :to="{ path: '/' }">
			    <i class="v-icon v-home" aria-hidden="true"
			        data-toggle="tooltip" data-placement="bottom" title="Home"
			    ></i>
			</router-link>
        </div>


        <aside class="menu">
        	<div class="flex-space">
	            <p class="menu-label">
	                Subscribed Channels <span v-if="Store.subscribedCategories.length">({{ Store.subscribedCategories.length }})</span>
	            </p>

        		<div class="ui icon top right active-blue pointing dropdown sidebar-panel-button">
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
    		</div>

            <div class="ui category search side-box-search">
                <div class="ui mini icon input">
                  <input class="prompt" type="text" placeholder="Channels..." v-model="subscribedFilter">
                  <i class="v-icon v-search search icon"></i>
                </div>
            </div>

            <div class="no-subsciption" v-if="!Store.subscribedCategories.length && !Store.loading">
            	No subscribed #channels
            </div>

            <ul class="menu-list" v-else>
                <li v-for="category in sortedSubscribeds">
    				<router-link :to="'/c/' + category.name">
    					<img class="square" v-bind:src="category.avatar" v-bind:alt="category.name">
                        <span class="v-channels-text">{{ category.name }}</span>
    				</router-link>
                </li>
            </ul>
        </aside>
    </div>
</template>

<script>
import Helpers from '../mixins/Helpers';
import LocalStorage from '../mixins/LocalStorage';

export default {
	mixins: [Helpers, LocalStorage],

    data: function () {
        return {
            subscribedFilter: '',
            auth,
            Store,
            filter: 'test'
        };
    },

	watch: {
		'$route': function () {
			this.subscribedFilter = ''
		}
	},

    computed: {
        submitURL(){
            if (this.$route.params.name)
            	return "/submit?channel=" + this.$route.params.name

            return "/submit"
        },

    	/**
    	 * The sorted version of comments
    	 *
    	 * @return {Array} comments
    	 */
    	sortedSubscribeds () {
			var self = this

    		return _.orderBy(Store.subscribedCategories.filter(function (category) {
				return category.name.indexOf(self.subscribedFilter.toLowerCase()) !== -1
			}), 'subscribers', 'desc').slice(0, 5)
    	},
    },

    methods: {
    	/**
    	 * changes the filter for sidebar
    	 *
    	 * @return void
    	 */
    	changeFilter() {
    	    return
    	},

        changeRoute: function(newRoute) {
        	this.$eventHub.$emit('new-route', newRoute)
        },
    },
}
</script>
