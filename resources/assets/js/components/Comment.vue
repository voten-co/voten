<template>
    <transition name="fade">
        <div class="comment v-comment-wrapper" v-show="visible" @mouseover="seen" :id="'comment' + list.id"
	        :class="highlightClass"
        >
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
                            <small><span v-tooltip.top="{content: 'Created: ' + longDate}">{{ date }}</span> — {{ points }} Points</small>
                        </router-link>

                        <small v-else><span v-tooltip.top="{content: 'Created: ' + longDate}">{{ date }}</span> — {{ points }} Points</small>

                        <span class="edited" v-if="isEdited" v-tooltip.top="{content: 'Edited: ' + editedDate}">
                            Edited
                        </span>
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
                    <router-link class="reply h-green" :to="'/submission/' + list.submission_id" v-if="!full" v-tooltip.top="{content: 'Submission'}">
                        <i class="v-icon v-link"></i>
                    </router-link>

                    <a class="reply h-green" @click="commentReply" v-if="list.level < 8 && full" v-tooltip.top="{content: 'Reply'}">
                        <i class="v-icon v-reply"></i>
                    </a>

                    <a class="reply h-primary" @click="voteUp">
                        <i class="v-icon v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
                    </a>

                    <a class="reply h-red" @click="voteDown">
                        <i class="v-icon v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
                    </a>

                    <a class="reply h-yellow" @click="bookmark" v-tooltip.top="{content: bookmarked ? 'Unbookmark' : 'Bookmark'}">
                        <i class="v-icon" v-bind:class="{ 'go-yellow v-unbookmark': bookmarked, 'v-bookmark': !bookmarked }"></i>
                    </a>

                    <a class="reply h-purple" @click="edit" v-if="owns" v-tooltip.top="{content: 'Edit'}">
                        <i class="v-icon v-edit"></i>
                    </a>

                    <div class="ui icon top left pointing dropdown" v-if="!isGuest"
                         id="more-button">
                        <i class="v-icon v-more" aria-hidden="true"></i>

                        <div class="menu">
                            <button class="item" @click="report" v-if="!owns">
                                Report
                            </button>

                            <button class="item" @click="destroy" v-if="owns">
                                Delete
                            </button>

                            <button class="item" @click="approve" v-if="showApprove">
                                Approve
                            </button>

                            <button class="item" @click="disapprove" v-if="showDisapprove">
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

            <button class="v-button v-button--link" v-if="hasMoreCommentsToLoad" @click="loadMoreComments">
	        	Load More Comments ({{ list.children.length - childrenLimit }} more replies)
	    	</button>
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

    .broadcasted-comment {
	    background: #f9f9f9 !important;
    }

    .child-broadcasted-comment {
	    background: #f9f9f9 !important;
	    border: 2px dashed #e9e9e9 !important;
	    padding-bottom: .7em !important;
        padding-right: 1em !important;
	    border-left: 2px dashed #e9e9e9 !important;
    }

    .edited {
        background: #f4f4f4;
        font-size: 10px;
        padding: 4px 4px;
        border-radius: 2px;
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
                childrenLimit: 4,
                highlighted: false,
            }
        },

        created () {
        	this.setBookmarked();
        	this.setVoteds();
            this.$eventHub.$on('newComment', this.newComment);
            this.$eventHub.$on('patchedComment', this.patchedComment);
            this.$eventHub.$on('deletedComment', this.deletedComment);
        },

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticDropdown('comment' + this.list.id);
                this.setHighlighted();
                this.scrollToComment();
            });
		},

		watch: {
            '$route' () {
                this.setBookmarked();
                this.setVoteds();
            },

            'Store.commentUpVotes' () {
                this.setVoteds();
            },

            'Store.commentDownVotes' () {
                this.setVoteds();
            },

            'Store.commentBookmarks' () {
                this.setBookmarked();
            },
		},

        computed: {
            isParent() {
                return this.list.parent_id == 0 ? true : false;
            },

            highlightClass() {
                if (this.highlighted && !this.isParent) {
                    return 'child-broadcasted-comment';
                }

                if (this.highlighted && this.isParent) {
                    return 'broadcasted-comment';
                }

                return '';
            },

            isEdited() {
                return this.list.edited_at;
            },

            editedDate() {
                return this.parseFullDate(this.list.edited_at); 
            }, 

            points() {
                let total = this.list.upvotes - this.list.downvotes;

				if (total < 0 ) return 0;

				return total;
            },


            /**
        	 * Does the auth user own the submission
        	 *
        	 * @return Boolean
        	 */
        	owns() {
        		return auth.id == this.list.user_id;
        	},

        	/**
        	 * is there more children to load
        	 *
        	 * @return bool
        	 */
        	hasMoreCommentsToLoad() {
        	    return this.list.children.length > this.childrenLimit;
        	},

        	/**
        	 * The sorted version of comments
        	 *
        	 * @return {Array} comments
        	 */
        	sortedComments() {
        		return _.orderBy(this.uniqueList, this.commentsOrder, 'desc')
        			.slice(0, this.childrenLimit);
        	},

        	/**
			 * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
			 * timestamps) we need a different approch to make sure the list is always unique.
			 * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
			 *
			 * @return object
			 */
			uniqueList() {
				let unique = []
				let temp = []

				this.list.children.forEach(function(element, index, self) {
					if (temp.indexOf(element.id) === -1) {
						unique.push(element)
						temp.push(element.id)
					}
				});

				return unique;
			},

            /**
             * The current vote type. It's being used to optimize the voting request on the server-side
             */
			currentVote () {
			    if (this.upvoted) {
			    	return "upvote";
			    }

				if (this.downvoted) {
					return "downvote";
				}

				return null;
			},

            date () {
            	return moment(this.list.created_at).utc(moment().format("Z")).fromNow();
            },

            /**
             * Calculates the long date to display for hover over date.
             *
             * @return String
             */
            longDate() {
                return this.parseFullDate(this.list.created_at);
            },

            /**
        	 * whether or not the approve button shoud be displayed
        	 *
        	 * @return boolean
        	 */
            showApprove() {
				return !this.list.approved_at && Store.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns
			},

            /**
        	 * whether or not the disapprove button shoud be displayed
        	 *
        	 * @return boolean
        	 */
			showDisapprove() {
				return !this.list.deleted_at && Store.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns
			},
        },


        methods: {
            /**
             * Sets the initial values for whether or not highlight the comment.
             *
             * @return void
             */
            setHighlighted() {
                if (this.list.broadcasted == true || this.$route.query.comment == (this.list.id)) {
                    this.highlighted = true;
                }
            },

            /**
             * Scrolls the page to the comment
             *
             * @return void
             */
            scrollToComment() {
                if (this.$route.query.comment == (this.list.id)) {
                    document.getElementById('comment' + this.list.id).scrollIntoView();
                }
            },

        	/**
        	 * renders more comments
        	 *
        	 * @return void
        	 */
        	loadMoreComments() {
        	    this.childrenLimit += 4;
        	},

        	/**
        	 * seen the comment
        	 *
        	 * @return void
        	 */
        	seen() {
        	    this.highlighted = false;
        	},

            edit() {
                this.editing = !this.editing;
            },

            patchComment(body) {
                this.editing = false;
                this.list.body = body;
                this.list.edited_at = moment().utc().format('YYYY-MM-DD HH:mm:ss');
            },

        	/**
             * whether or not the user has voted on comment
             *
             * @return void
             */
            setVoteds () {
            	if (Store.commentUpVotes.indexOf(this.list.id) != -1) {
            		this.upvoted = true;
            		this.downvoted = false;
            		return;
            	}

            	if (Store.commentDownVotes.indexOf(this.list.id) != -1) {
            		this.downvoted = true;
            		this.upvoted = false;
            		return;
            	}

                this.downvoted = false;
                this.upvoted = false;
            },

        	/**
             * whether or not user has bookmarked the comment
             *
             * @return void
             */
            setBookmarked() {
            	if(Store.commentBookmarks.indexOf(this.list.id) != -1) {
            		this.bookmarked = true;
            	} else {
                    this.bookmarked = false;
                }
            },

        	newComment(comment) {
                if (this.list.id != comment.parent_id) return;

                // owns the comment
                if(comment.owner.id == auth.id) {
                    this.reply = false;
                    Store.commentUpVotes.push(comment.id);
                    this.list.children.unshift(comment);
                    return;
                }

                // add broadcasted (used for styling)
				comment.broadcasted = true;

                this.list.children.unshift(comment);
        	},

        	/**
	    	 * patches the broadcasted comment.
	    	 *
	    	 * @return void
	    	 */
            patchedComment(comment) {
            	if (this.list.id != comment.id) return;

            	this.list.body = comment.body;
                this.list.edited_at = moment().utc().format('YYYY-MM-DD HH:mm:ss');
            },

            /**
             *  Report(and block) comment
             */
            report() {
        		this.$eventHub.$emit('report-comment', this.list.id);
            },

            /**
             *  Toggles the comment into bookmarks
             */
        	bookmark() {
        		if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

        		this.bookmarked = !this.bookmarked;

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
            commentReply () {
            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

            	this.reply = !this.reply;
        	},

            /**
             *  upVote comment
             *
             *  @return void
             */
            voteUp () {
            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

				let id = this.list.id;

				axios.post('/upvote-comment', {
                    comment_id: id,
                    previous_vote: this.currentVote
                });

            	// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false;
            		this.list.upvotes --;

            		var index = Store.commentUpVotes.indexOf(id);
                	Store.commentUpVotes.splice(index, 1);

            		return;
            	}

				// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false;
            		this.list.downvotes --;

            		var index = Store.commentDownVotes.indexOf(id);
                	Store.commentDownVotes.splice(index, 1);
            	}

            	// Not voted
            	this.upvoted = true;
            	this.list.upvotes ++;
            	Store.commentUpVotes.push(id);
            },


            /**
             *  downVote comment
             *
             *  @return void
             */
            voteDown () {
            	if (this.isGuest) {
            		this.mustBeLogin();
            		return;
            	}

				let id = this.list.id;

				axios.post('/downvote-comment', {
                    comment_id: id,
                    previous_vote: this.currentVote
                });

            	// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false;
            		this.list.downvotes --;

            		var index = Store.commentDownVotes.indexOf(id);
                	Store.commentDownVotes.splice(index, 1);

            		return;
            	}

				// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false;
            		this.list.upvotes --;

            		var index = Store.commentUpVotes.indexOf(id);
                	Store.commentUpVotes.splice(index, 1);
            	}

            	// Not voted
            	this.downvoted = true;
            	this.list.downvotes ++;
            	Store.commentDownVotes.push(id);
            },

            /**
             * Deletes the comment. Only the owner is allowed to make such decision.
             *
             * @return void
             */
            destroy() {
                this.visible = false;
                
                axios.post('/destroy-comment', { id: this.list.id });
            },

            /**
             * deletes the broadcasted comment
             *
             * @return void
             */
            deletedComment(comment) {
            	if (comment.id != this.list.id) return;

                this.visible = false;
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
				    this.list.approved_at = moment().utc().format('YYYY-MM-DD HH:mm:ss');
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
					this.visible = false;
				})
			},
        }
    }
</script>
