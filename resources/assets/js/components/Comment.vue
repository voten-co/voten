.tsil<template>
    <transition name="fade">
        <div class="comment v-comment-wrapper" v-show="visible">
            <div class="content">
                <div class="v-comment-info">
                    <router-link :to="'/' + '@' + list.owner.username" class="avatar user-select">
                        <img v-bind:src="list.owner.avatar">
                    </router-link>

                    <router-link :to="'/' + '@' + list.owner.username" class="author user-select">
                        @{{ list.owner.username }}
                    </router-link>

                    <div class="metadata user-select">
                        <router-link class="go-gray h-underline" v-if="!full"
                        :to="'/submission/' + list.submission_id">
                            <small><span data-toggle="tooltip" data-placement="bottom" :title="'Created: ' + longDate">{{ date }}</span> - {{ points }} Points</small>
                        </router-link>

                        <small v-else><span data-toggle="tooltip" data-placement="bottom" :title="'Created: ' + longDate">{{ date }}</span> - {{ points }} Points</small>
                    </div>
                </div>

                <div class="text">
                    <comment-form :submission="list.submission_id" :parent="list.id"
                        :editing="editing" v-if="editing" :before="list.body" :id="list.id"
                            @patched-comment="patchComment"
                        ></comment-form>

                    <markdown :text="list.body" v-else></markdown>
                </div>

                <div class="actions user-select">
                    <a class="reply" @click="commentReply" v-if="list.level < 8"
                    data-toggle="tooltip" data-placement="top" title="Reply">
                        <i class="v-icon v-reply h-green"></i>
                    </a>

                    <a class="reply" @click="voteUp"
                    data-toggle="tooltip" data-placement="top" title="Upvote">
                        <i class="v-icon h-primary v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
                    </a>

                    <a class="reply" @click="voteDown"
                    data-toggle="tooltip" data-placement="top" title="Downvote">
                        <i class="v-icon h-red v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
                    </a>

                    <a class="reply" @click="bookmark"
                    data-toggle="tooltip" data-placement="top" title="Bookmark">
                        <i class="v-icon h-yellow" v-bind:class="{ 'go-yellow v-unbookmark': bookmarked, 'v-bookmark': !bookmarked }"></i>
                    </a>

                    <a class="reply" @click="edit" v-if="owns"
                    data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="v-icon v-edit h-purple"></i>
                    </a>

                    <div class="ui icon top right pointing dropdown" data-toggle="tooltip" data-placement="top" title="More">
                        <i class="v-icon v-more" aria-hidden="true"></i>

                        <div class="menu">
                            <button class="item" @click="report" v-if="!owns">
                                <i class="v-icon v-flag-empty go-green" aria-hidden="true"></i>
                                Report
                            </button>

                            <button class="item" @click="destroy" v-if="owns">
                                <i class="v-icon v-trash go-red" aria-hidden="true"></i>
                                Delete
                            </button>

                            <button class="item" @click="approve" v-if="showApprove">
                                <i class="v-icon v-approve go-primary" aria-hidden="true"></i>
                                Approve
                            </button>

                            <button class="item" @click="disapprove" v-if="showDisapprove">
                                <i class="v-icon v-trash go-red" aria-hidden="true"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <span v-if="reply">
                    <comment-form :submission="list.submission_id" :parent="list.id"></comment-form>
                </span>
            </div>

            <div class="comments" v-if="list.children.length">
                <comment :list="c" v-for="c in sortedComments" :key="c.id" :full="full"></comment>
            </div>
        </div>
    </transition>
</template>


<style>
    .v-comment-wrapper {
        /*padding: 1em !important;*/
    }

    .v-comment-info {
        display: flex;
        align-items: center;
    }
</style>


<script>
    import CommentForm from '../components/CommentForm.vue';
    import Markdown from '../components/Markdown.vue';
    import Helpers from '../mixins/Helpers';

    export default {

        name: 'comment',

        props: ['list', 'comments-order', 'full'],

        components: {
            CommentForm,
            Markdown
        },
        
        mixins: [Helpers],

        data() {
            return {
                editing: false,
                body: this.list,
                visible: true,
                bookmarked: false,
                upvoted: false,
                downvoted: false,
                reply: false,
                auth,
                Store
            }
        },

        created () {
        	this.setBookmarked()
        	this.setVoteds()
            this.$eventHub.$on('newComment', this.newComment)
        },

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip()
	        	this.$root.loadSemanticDropdown()
			})
		},

        computed: {
            points() {
                let total = this.list.upvotes - this.list.downvotes

				if (total < 0 ) return 0

				return total
            },


            /**
        	 * Does the auth user own the submission
        	 *
        	 * @return Boolean
        	 */
        	owns () {
        		return auth.id == this.list.user_id
        	},

        	/**
        	 * The sorted version of comments
        	 *
        	 * @return {Array} comments
        	 */
        	sortedComments () {
        		return _.orderBy(this.list.children, this.commentsOrder, 'desc')
        	},

            /**
			 * The current vote type. It's being used to optimize the voing request on the server-side.
			 *
			 * @return mixed
			 */
			currentVote () {
			    if (this.upvoted) {
			    	return "upvote"
			    }

				if (this.downvoted) {
					return "downvote"
				}

				return null;
			},

            date () {
            	return moment(this.list.created_at).utc(moment().format("Z")).fromNow()
            },

            /**
            * Calculates the long date to display for hover over date.
            *
            * @return String
            */
            longDate () {
                return this.parseFullDate(this.list.created_at);
            },

            /**
        	 * whether or not the approve button shoud be displayed
        	 *
        	 * @return boolean
        	 */
            showApprove(){
				return !this.list.approved_at && Store.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns
			},

            /**
        	 * whether or not the disapprove button shoud be displayed
        	 *
        	 * @return boolean
        	 */
			showDisapprove(){
				return !this.list.deleted_at && Store.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns
			},
        },


        methods: {
            edit() {
                this.editing = !this.editing
            },

            patchComment(body) {
                this.editing = false

                this.list.body = body
            },

        	/**
            *  whether or not the user has voted on comment
            *
            *  @return void
            */
            setVoteds () {
            	if (Store.commentUpVotes.indexOf(this.list.id) != -1) {
            		this.upvoted = true
            		return
            	}

            	if (Store.commentDownVotes.indexOf(this.list.id) != -1) {
            		this.downvoted = true
            		return
            	}

            	return
            },

        	/**
            *  whether or not user has bookmarked the comment
            *
            *  @return void
            */
            setBookmarked () { if(Store.commentBookmarks.indexOf(this.list.id) != -1) this.bookmarked = true },

        	newComment (comment) {
                if (this.list.id == comment.parent_id) {
                    if(comment.owner.id == auth.id){
                        this.reply = false;
                        Store.commentUpVotes.push(comment.id);
                        this.list.children.unshift(comment);
                        return
                    }

                    this.list.children.unshift(comment);
                }
        	},

            /**
            *  Report(and block) comment
            */
            report: function () {
        		this.$eventHub.$emit('report-comment', this.list.id)
            },

            /**
            *  Toggles the comment into bookmarks
            */
        	bookmark () {
        		this.bookmarked = !this.bookmarked

				axios.post('/bookmark-comment', {
					id: this.list.id,
				}).then((response) => {
					if (Store.commentBookmarks.indexOf(this.list.id) != -1){
	                	var index = Store.commentBookmarks.indexOf(this.list.id);
	                	Store.commentBookmarks.splice(index, 1);
	                	return;
	                }
					Store.commentBookmarks.push(this.list.id);
				});
        	},

        	/**
            *  toggles the reply form
            */
            commentReply () { this.reply = !this.reply },

            /**
            *  upVote comment
            *
            *  @return void
            */
            voteUp () {
				let id = this.list.id

				axios.post('/upvote-comment', {
                    comment_id: id,
                    previous_vote: this.currentVote
                 })

            	// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false
            		this.list.upvotes --
            		Store.commentUpVotes = Store.commentUpVotes.filter(function (item) {
					  	return item.id != id
					})
            		return
            	}

				// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false
            		this.list.downvotes --
            		Store.commentDownVotes = Store.commentDownVotes.filter(function (item) {
					  	return item.id != id
					})
            	}

            	// Not voted
            	this.upvoted = true
            	this.list.upvotes ++
            	Store.commentUpVotes.push(id)
            },


            /**
            *  downVote comment
            *
            *  @return void
            */
            voteDown () {
				let id = this.list.id

				axios.post('/downvote-comment', {
                    comment_id: id,
                    previous_vote: this.currentVote
                 })

            	// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false
            		this.list.downvotes --
            		Store.commentDownVotes = Store.commentDownVotes.filter(function (item) {
					  	return item.id != id
					})
            		return
            	}

				// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false
            		this.list.upvotes --
            		Store.commentUpVotes = Store.commentUpVotes.filter(function (item) {
					  	return item.id != id
					})
            	}

            	// Not voted
            	this.downvoted = true
            	this.list.downvotes ++
            	Store.commentDownVotes.push(id)
            },

            /**
             * Deletes the comment. Only the owner is allowed to make such decision.
             *
             * @return void
             */
            destroy() {
                axios.post('/destroy-comment', { id: this.list.id })
                this.visible = false
            },

            /**
             * Approves the comment. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
			approve(){
				axios.post('/approve-comment', {
				    comment_id: this.list.id
				}).then((response) => {
				    this.list.approved_at = moment().utc().format('YYYY-MM-DD HH:mm:ss')
				})
			},

			/**
             * Disapproves the comment. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
			disapprove(){
				axios.post('/disapprove-comment', {
				    comment_id: this.list.id
				}).then((response) => {
					this.visible = false
				})
			},
        }
    }
</script>
