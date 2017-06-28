<template>
<div>
	<category-header v-if="loaded && !auth.isMobileDevice"></category-header>

	<category-header-mobile v-if="loaded && auth.isMobileDevice"></category-header-mobile>

	<div class="col-full">
		<nsfw-warning v-if="submission.nsfw == 1 && !auth.nsfw"
			:text="'This submission contains NSFW content which can not be displayed according to your personal settings.'">
		</nsfw-warning>

		<div v-if="submission.nsfw == 0 || auth.nsfw">
			<loading v-if="loadingSubmission"></loading>

			<full-submission v-if="!loadingSubmission" :list="submission" :full="true"></full-submission>

		    <section class="box-typical comments" id="comments-section" v-if="!loadingSubmission">
		        <header class="box-typical-header-sm bordered user-select flex-space">
		            <div>
		            	<span v-show="comments.length">{{ submission.comments_number }}</span>
		            	Comments: <span class="go-gray go-small" v-if="!isGuest">({{ onlineUsers }} online users)</span>
		            </div>
		            <div class="head-sort-icon" v-show="comments.length > 1">
		                <i class="v-icon v-like pointer" aria-hidden="true"
		                   data-toggle="tooltip" data-placement="bottom" title="Hottest"
		                   @click="newSort('hot')"
		                   :class="{ 'go-primary': sort == 'hot' }"></i>
		                <i class="v-icon v-clock pointer" aria-hidden="true"
		                   data-toggle="tooltip" data-placement="bottom" title="Newest"
		                   @click="newSort('new')"
		                   :class="{ 'go-primary': sort == 'new' }"></i>
		            </div>
		        </header>

		        <div class="box-typical-inner ui threaded comments" v-if="submission.id != 0">
		            <comment-form :submission="submission.id" :parent="0"></comment-form>

		            <loading v-if="loadingComments && page < 2"></loading>

					<comment :list="c" :comments-order="commentsOrder" v-for="c in uniqueList" :key="c.id" :full="true"></comment>
		        </div>
		    </section>

		    <button class="v-button v-button--block" v-if="moreComments" @click="loadMoreComments">
	        	Load More Comments
	    	</button>
		</div>
	</div>
</div>
</template>

<script>
	import FullSubmission from '../components/FullSubmission.vue';
	import Comment from '../components/Comment.vue';
	import CommentForm from '../components/CommentForm.vue';
	import CategoryHeader from '../components/CategoryHeader.vue';
	import CategoryHeaderMobile from '../components/CategoryHeaderMobile.vue';
	import Loading from '../components/Loading.vue';
	import NsfwWarning from '../components/NsfwWarning.vue';
	import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

        components: {
            FullSubmission,
            Comment,
            CommentForm,
            Loading,
            CategoryHeader,
            CategoryHeaderMobile,
			NsfwWarning
        },

        data () {
            return {
            	page: 1,
            	moreComments: false,
                submission: [],
                loadingComments: true,
                loadingSubmission: true,
                comments: [],
                auth,
                sort: 'hot',
                onlineUsers: 0,
                category: this.$route.params.name,
                Store,
                preload
            }
        },

        created () {
            this.getSubmission();
            this.getComments();
            this.listen();
            this.$eventHub.$on('newComment', this.newComment);
            this.$eventHub.$on('patchedComment', this.patchedComment);
        },

	    watch: {
			'$route' () {
	            this.getSubmission();
	            this.getComments();
	            this.clearContent();
	            this.listen();
	            this.updateCategoryStore();
	            this.$eventHub.$on('newComment', this.newComment);
	            this.$eventHub.$on('patchedComment', this.patchedComment);
			}
		},

        computed: {
			/**
			 * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
			 * timestamps) we need a different approch to make sure the list is always unique.
			 * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
			 *
			 * @return array
			 */
			uniqueList() {
				let unique = []
				let temp = []

				this.comments.forEach(function(element, index, self) {
					if (temp.indexOf(element.id) === -1) {
						unique.push(element)
						temp.push(element.id)
					}
				})

				return unique;
			},

			/**
			 * Is the category store loaded yet
			 *
			 * @return bool
			 */
        	loaded () {
	            return Store.category.name == this.$route.params.name;
	        },

            /**
             * The order that comments should be printed with
             *
             * @return string
             */
            commentsOrder() {
            	return this.sort == 'hot' ? 'rate' : 'created_at';
            },
        },

        methods: {
        	/**
        	 * resets all the basic data to prevent possible conflicts
        	 *
        	 * @return void
        	 */
        	clearContent () {
        		this.moreComments = false;
        		this.page = 1;
        		this.comments = [];
        	},

        	loadMoreComments () {
        		this.page ++
                this.moreComments = false
        		this.getComments()
        	},

        	/**
	    	 * Checks wheather or not the Store.category needs to be filled or updated, and if yes simply does it
	    	 *
	    	 * @return void
	    	 */
	    	updateCategoryStore() {
	    		if (Store.category.name == undefined || Store.category.name != this.$route.params.name) {
		    		this.$root.getCategoryStore(this.$route.params.name);
		    		this.category = this.$route.params.name;
	    		}
	    	},

	    	/**
	    	 * receives the broadcasted comment.
	    	 *
	    	 * @return void
	    	 */
            newComment(comment) {
            	if (comment.parent_id != 0 || comment.submission_id != this.submission.id) return;

				// add broadcastedParent (used for styling)
				if (comment.user_id != auth.id) {
					comment.broadcastedParent = true;
				}

				this.comments.unshift(comment);
				this.submission.comments_number ++;
            },

            /**
	    	 * receives the broadcasted comment.
	    	 *
	    	 * @return void
	    	 */
            patchedComment(comment) {
            	if (comment.parent_id != 0 || comment.submission_id != this.submission.id) return;

            	let comment_id = comment.id;
            	function findObject(ob) {
	                return ob.id === comment_id;
	            }
	            let i = this.comments.findIndex(findObject);

	            if (i != -1) {
	            	this.comments[i].body = comment.body;
	            }
            },

            /**
             * listen for broadcasted comments
             *
             * @return void
             */
            listen() {
                Echo.channel('submission.' + this.$route.params.slug)
                    .listen('CommentCreated', event => {
                    	this.$eventHub.$emit('newComment', event.comment)
                    }).listen('CommentWasPatched', event => {
                    	this.$eventHub.$emit('patchedComment', event.comment)
                    });

                // we can't do presence channel if the user is a guest
                if (this.isGuest) return;

                Echo.join('submission.' + this.$route.params.slug)
				    .here((users) => {
				        this.onlineUsers = users.length
				    })
				    .joining((user) => {
				        this.onlineUsers ++
				    })
				    .leaving((user) => {
				        this.onlineUsers --
				    });
            },

            /**
             * Get submissions
             *
             * @return void
             */
            getSubmission() {
            	// if landed on a submission page
            	if (preload.submission) {
            		this.submission = preload.submission;
            		Store.category = preload.submission.category;
            		this.loadingSubmission = false;
            		delete preload.submission;
            		return;
            	}

                axios.get('/get-submission', {
            		params: {
            			slug: this.$route.params.slug
            		}
            	}).then((response) => {
					this.submission = response.data
					this.setPageTitle(this.submission.title);

                    if( !this.loaded ) {
                    	Store.category = response.data.category
                    }

                    this.loadingSubmission = false
				}).catch((error) => {
					if (error.response.status === 404) {
						this.$router.push('/404')
					}
				});
            },

            /**
             * get comments
             *
             * @return void
             */
            getComments () {
                this.loadingComments = true

                axios.get('/submission-comments', {
                	params: {
	                	submission_slug: this.$route.params.slug,
	                	page: this.page,
	                	sort: this.sort
                	}
                }).then((response) => {
                	this.loadingComments = false

                    this.comments.push(...response.data.data)

                    if (response.data.next_page_url != null) {
                    	this.moreComments = true
                    }
                })
            },

            newSort(sort) {
            	if (sort == this.sort) return;

            	this.clearContent();
            	this.getComments();
                this.sort = sort;
            }
        },

        /**
         * necessary actions before leaving this submission page
         *
         * @return void
         */
        beforeRouteLeave(to, from, next) {
        	Echo.leave('submission.' + from.params.slug);

			next();
		}
    }

</script>
