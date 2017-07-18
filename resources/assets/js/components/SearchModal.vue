<template>
	<div class="v-modal" id="search">
	    <div class="v-close" data-toggle="tooltip" data-placement="bottom" title="Close (esc)" @click="close">
	        <i class="v-icon block-before v-cancel" aria-hidden="true"></i>
	    </div>

	    <div class="v-modal-title user-select">
	        <h1 class="title">
	            Search
	        </h1>

			<a href="https://www.algolia.com/referrals/fb684d54/join" target="_blank" rel="nofollow" class="margin-left-half">
				<img src="/imgs/algolia-powered-by.svg" alt="search by algolia">
			</a>

	        <h4 class="sub-title">
	            Let's find you whatever you're looking for
	        </h4>
	    </div>

	    <div class="v-modal-search-box" :class="{ 'left-1': !sidebar }">
	    	<div class="tabs is-fullwidth margin-bottom-1">
				<ul>
					<li :class="{ 'is-active--green': type == 'Categories'}">
						<a class="h-green" @click="changeType('Categories')">
							<span>Channels</span>
						</a>
					</li>

					<li :class="{ 'is-active--green': type == 'Users'}">
						<a class="h-green" @click="changeType('Users')">
							<span>Users</span>
						</a>
					</li>

					<li :class="{ 'is-active--green': type == 'Submissions'}">
						<a class="h-green" @click="changeType('Submissions')">
							<span>Submissions</span>
						</a>
					</li>

					<li :class="{ 'is-active--green': type == 'Comments'}">
						<a class="h-green" @click="changeType('Comments')">
							<span>Comments</span>
						</a>
					</li>
				</ul>
			</div>

            <div class="ui contacts search">
                <div class="ui huge icon input">
                    <input class="v-search" v-model="filter" type="text"
						   :placeholder="placeholder"
						   v-on:input="search(filter)" v-focus="focused" @focus="focused = true">

					<i v-show="!loading" class="v-icon v-search search icon"></i>
		        	<moon-loader :loading="loading" :size="'30px'" :color="'#777'"></moon-loader>
                </div>
            </div>
	    </div>

	    <div class="container">
	    	<div class="v-push-20"></div>

	        <div class="col-7 user-select">
	            <ul class="v-contact-list" v-if="type == 'Categories'">
	            	<category-search-item v-for="category in categories" :list="category" :key="category.id"></category-search-item>
	            </ul>

	            <h1 class="align-center" v-if="noCategories && filter">
	            	No #channel matched your keywords
	            </h1>

	            <ul class="v-search-items" v-if="type == 'Users'">
	            	<user-search-item v-for="user in users" :list="user" :key="user.id"></user-search-item>
	            </ul>

	            <h1 class="align-center" v-if="noUsers && filter">
	            	No user matched your keywords
	            </h1>

	            <div v-if="type == 'Submissions'">
			        <submission v-for="submission in submissions" :list="submission" :key="submission.id"></submission>

			        <h1 class="align-center" v-if="noSubmissions && filter">
		            	No submission matched your keywords
		            </h1>
			    </div>

		    	<div class="ui threaded comments" v-if="type == 'Comments'">
		    		<div v-for="comment in comments" class="comment-search-item" :key="comment.id">
				        <comment :list="comment" :comments-order="'created_at'"></comment>
				    </div>

				    <h1 class="align-center" v-if="noComments && filter">
		            	No comment matched your keywords
		            </h1>
		    	</div>
	        </div>
	    </div>
	</div>
</template>

<script>
import CategorySearchItem from '../components/CategorySearchItem.vue';
import UserSearchItem from '../components/UserSearchItem.vue';
import Submission from '../components/Submission.vue';
import Comment from '../components/Comment.vue';
import MoonLoader from '../components/MoonLoader.vue';
import { focus } from 'vue-focus';

export default {
	directives: {
		focus: focus
	},

	components: {
		CategorySearchItem,
		Comment,
		Submission,
		MoonLoader,
		UserSearchItem
	},

	props: ['sidebar'],

	data: function () {
		return {
			focused: true,
			filter: '',
			result: [],
			loading: false,
			categories: [],
			users: [],
			submissions: [],
			comments: [],
			type: 'Categories',
		}
	},

	mounted: function () {
		this.$nextTick(function () {
			this.$root.loadSemanticTooltip()
		})
	},


	computed: {
		noSubmissions() {
			return this.type == 'Submissions' && this.submissions.length == 0;
		},

		noComments() {
			return this.type == 'Comments' && this.comments.length == 0;
		},

		noCategories() {
			return this.type == 'Categories' && this.categories.length == 0;
		},

		noUsers() {
			return this.type == 'Users' && this.users.length == 0;
		},

		placeholder() {
		    if (this.type == 'Categories') {
		        return 'Search by #name or description...';
			}

			if (this.type == 'Users') {
		        return 'Search by @username or name...';
			}

            if (this.type == 'Submissions') {
                return 'Search by title...';
            }

            if (this.type == 'Comments') {
                return 'Search by content...';
            }

            return 'Search...';
		}
	},

	methods: {
		/**
    	 * Gets the data with ajax call and put it in the correct array
    	 */
		search: _.debounce(function () {
			if(!this.filter.trim()) return

			this.loading = true;

			axios.get('/search', {
				params: {
	                type: this.type,
	                searched: this.filter,
				}
            }).then((response) => {
            	if( this.type == 'Categories' ) {
            		this.categories = response.data;
            	}

            	if( this.type == 'Users' ) {
            		this.users = response.data;
            	}

            	if( this.type == 'Comments' ) {
            		this.comments = response.data;
            	}

            	if( this.type == 'Submissions' ) {
            		this.submissions = response.data;
            	}

            	this.loading = false;
            }).catch((error) => {
            	this.loading = false;
            });
		}, 600),

		/**
    	 * Changes the type of the search
    	 * @return void
    	 */
    	changeType: function (type) {
    		if(this.type == type) return

			this.type = type
			this.search()
		},

    	/**
    	 * Fires the 'close' event which causes all the modals to be closed.
    	 *
    	 * @return void
    	 */
    	close () {
    		this.$eventHub.$emit('close')
    	}
	}
}
</script>
