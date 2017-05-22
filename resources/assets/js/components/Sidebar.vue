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
            <p class="menu-label">
                <i class="v-icon v-channels" aria-hidden="true"></i>
                Subscribed Channels <span v-if="Store.subscribedCategories.length">({{ Store.subscribedCategories.length }})</span>
            </p>

            <div class="ui category search side-box-search">
                <div class="ui mini icon input">
                  <input class="prompt" type="text" placeholder="Subscribed Channels..." v-model="subscribedFilter">
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
export default {

    data: function () {
        return {
            subscribedFilter: '',
            auth,
            Store
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
        changeRoute: function(newRoute) {
        	this.$eventHub.$emit('new-route', newRoute)
        },
    },
}
</script>
