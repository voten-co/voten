<template>
    <div class="side-fixed"  id="v-sidebar">
        <div class="sidebar-offer-wrapper">
        	<h3>
        		New to Voten?
        	</h3>

        	<p>
        		Sign up now to get your own personalized timeline, modified sidebar, customizable design, and real-time experience!
        	</p>

        	<button class="v-button v-button--block" @click="signUp">
        		Sign up
        	</button>
        </div>

        <aside class="menu">
        	<div class="flex-space">
	            <p class="menu-label">
	                #Recommendeds<span v-if="Store.subscribedCategories.length">({{ Store.subscribedCategories.length }})</span>
	            </p>

				<div class="ui icon top right active-blue pointing dropdown sidebar-panel-button">
	            	<i class="v-icon v-config" @click="mustBeLogin"></i>
				</div>
        	</div>

            <div class="ui category search side-box-search">
                <div class="ui mini icon input">
                  <input class="prompt" type="text" placeholder="Channels..." spellcheck="false"
						 v-model="subscribedFilter">
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

        <hr>

        <ul class="sidebar-copyright">
        	<li>&copy; 2017 Voten</li>
        	<li><a href="/about">About</a></li>
        	<li><router-link to="/help">Help Center</router-link></li>
        	<li><a href="/tos">Terms</a></li>
        	<li><a href="https://medium.com/voten" target="_blank">Blog</a></li>
        	<li><a href="/privacy-policy">Privacy Policy</a></li>
        	<li><a href="/credits">Credits</a></li>
        	<li><a href="mailto:info@voten.co">Contact</a></li>
        	<li><a href="mailto:press@voten.co">Press</a></li>
        	<li><a href="https://github.com/voten-co/voten" target="_blank">Source code</a></li>
        </ul>
    </div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
	mixins: [Helpers],

    data: function () {
        return {
            subscribedFilter: '',
            auth,
            Store
        };
    },

	watch: {
		'$route': function () {
			this.subscribedFilter = '';
		}
	},

	created() {
		axios.get(this.authUrl('sidebar-categories')).then((response) => {
	    	Store.subscribedCategories = response.data;
	    });
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
				return category.name.toLowerCase().indexOf(self.subscribedFilter.toLowerCase()) !== -1
			}), 'subscribers', 'desc').slice(0, 5)
    	},
    },

    methods: {
        changeRoute(newRoute) {
        	this.$eventHub.$emit('new-route', newRoute)
        },

        signUp() {
        	if (this.isMobile) {
        		this.$eventHub.$emit('toggle-sidebar');
        	}

            this.mustBeLogin();
        }
    },
}
</script>
