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
	                {{ categoriesTitle }} <span v-if="Store.subscribedCategories.length">({{ Store.subscribedCategories.length }})</span>
	            </p>

        		<div class="ui icon top right active-blue pointing dropdown sidebar-panel-button">
					<i class="v-icon v-config"></i>

					<div class="menu">
						<div class="header">
							Limit to
						</div>
						<div class="ui left input">
							<input type="number" name="search" placeholder="Limit at..." min="2" v-model="categoriesLimit">
						</div>

						<div class="header">
							Filter by
						</div>
						<button class="item" @click="changeFilter('subscribed-channels')" :class="{ 'active' : filter == 'subscribed-channels' }">
							Subscribed channels
						</button>

						<button class="item" @click="changeFilter('moderating-channels')" :class="{ 'active' : filter == 'moderating-channels' }" v-if="isModerating">
							Moderating channels
						</button>

						<button class="item" @click="changeFilter('bookmarked-channels')" :class="{ 'active' : filter == 'bookmarked-channels' }">
							Bookmarked channels
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
            	<i class="v-icon v-sad" aria-hidden="true"></i>
            	No channels to display
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
            categoriesLimit: 5,
        };
    },

	watch: {
		'$route': function () {
			this.subscribedFilter = ''
		},

		'categoriesLimit' : function () {
			// console.log(this.categoriesLimit)
			this.putLS('sidebar-categories-limit', this.categoriesLimit);
		}
	},

	created() {
		if (this.isSetLS('sidebar-categories-limit')) {
			this.categoriesLimit = this.getLS('sidebar-categories-limit');
		}
	},

    computed: {
    	filter() {
    		return Store.sidebarFilter;
    	},

    	categoriesTitle() {
    		if (Store.sidebarFilter == "moderating-channels") {
    			return "Moderating Channels";
    		}

    		if (Store.sidebarFilter == "bookmarked-channels") {
    			return "Bookmarked Channels";
    		}

    		return "Subscribed Channels";
    	},

        submitURL() {
            if (this.$route.params.name)
            	return "/submit?channel=" + this.$route.params.name

            return "/submit"
        },

    	/**
    	 * The sorted version of comments
    	 *
    	 * @return {Array} comments
    	 */
    	sortedSubscribeds() {
			var self = this

    		return _.orderBy(Store.subscribedCategories.filter(function (category) {
				return category.name.indexOf(self.subscribedFilter.toLowerCase()) !== -1
			}), 'subscribers', 'desc').slice(0, (this.categoriesLimit > 2 ? this.categoriesLimit : 2))
    	},
    },

    methods: {
    	/**
    	 * changes the filter for sidebar
    	 *
    	 * @return void
    	 */
    	changeFilter(filter) {
    		if (Store.sidebarFilter == filter) return;

    	    Store.sidebarFilter = filter;

    	    this.putLS('sidebar-filter', filter);

    	    axios.get(this.authUrl('sidebar-categories'), {
    	    	params: {
    	    		sidebar_filter: Store.sidebarFilter
    	    	}
    	    }).then((response) => {
    	    	Store.subscribedCategories = response.data;
    	    });
    	},

        changeRoute: function(newRoute) {
        	this.$eventHub.$emit('new-route', newRoute)
        },
    },
}
</script>
