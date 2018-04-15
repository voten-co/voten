<style lang="scss">
.comment {
    .v-comment-info {
        display: flex;
        justify-content: space-between;
    }

    .left {
        display: flex;
        align-items: center;
    }

    .actions-right {
        display: flex;
    }

    .separator {
        margin-left: 0.5em;
        margin-right: 0.5em;
        color: #979797;
        font-weight: bold;
    }

    .date {
        color: #979797;
        font-size: 70%;
        cursor: pointer;

        &:hover {
            text-decoration: underline;
        }
    }

    .like-button {
        display: inline-flex;
        align-items: center;
        cursor: pointer;

        .count {
            font-weight: bold;
            font-size: 80%;
            color: #979797;
            margin-right: 1em;
        }

        &:hover {
            i {
                color: #db6e6e !important;
            }

            .count {
                color: #db6e6e;
            }
        }
    }

    .v-bookmark:hover {
        color: #edb431;
    }

    .v-reply:hover {
        color: #78b38a;
    }

    .el-icon-more-outline:hover {
        color: #000;
    }
}
</style>


<template>
	<transition name="el-fade-in-linear">
		<div class="comment v-comment-wrapper"
		     v-show="visible"
		     @mouseover="seen"
		     :id="'comment' + list.id"
		     :class="highlightClass">
			<div class="content"
			     @dblclick="doubleClicked">
				<div class="v-comment-info">
					<div class="display-flex">
						<div class="left">
							<router-link :to="'/' + '@' + list.author.username"
							             class="avatar user-select">
								<img v-bind:src="list.author.avatar">
							</router-link>

							<router-link :to="'/' + '@' + list.author.username"
							             class="author user-select">
								@{{ list.author.username }}
							</router-link>

							<span class="separator">
								&#183;
							</span>

							<a class="like-button"
							   @click="like">
								<i class="v-icon"
								   :class="liked ? 'v-heart-filled go-red animated bounceIn' : 'v-heart go-gray'"></i>

								<span class="count">{{ points }}</span>
							</a>

							<el-tooltip :content="bookmarked ? 'Unbookmark' : 'Bookmark'"
							            placement="top"
							            transition="false"
							            :open-delay="500">
								<i class="v-icon margin-left-1"
								   :class="{ 'go-yellow v-unbookmark': bookmarked, 'v-bookmark': !bookmarked }"
								   @click="bookmark"></i>
							</el-tooltip>

							<el-tooltip content="Reply"
							            placement="top"
							            transition="false"
							            :open-delay="500">
								<i class="v-icon v-reply margin-left-1"
								   @click="commentReply"
								   v-if="list.nested_level < 8 && full"></i>
							</el-tooltip>

							<el-tooltip content="Submission"
							            placement="top"
							            transition="false"
							            :open-delay="500">
								<a class="reply margin-left-1"
								   v-if="!full"
								   @click.prevent="openOrigin">
									<i class="v-icon v-link h-purple"></i>
								</a>
							</el-tooltip>

							<el-button size="mini"
							           class="margin-left-1"
							           v-if="owns && full"
							           @click="edit"
							           round>
								Edit
							</el-button>
						</div>
					</div>

					<div class="actions-right">
						<el-tooltip :content="'Edited: ' + editedDate"
						            placement="top"
						            transition="false"
						            :open-delay="500"
						            v-if="isEdited">
							<span class="edited go-gray">
								Edited
							</span>
						</el-tooltip>

						<span class="separator"
						      v-if="isEdited">
							&#183;
						</span>

						<el-tooltip :content="'Created: ' + longDate"
						            placement="top"
						            transition="false"
						            :open-delay="500">
							<a class="date margin-right-1"
							   @click.prevent="openOrigin">
								{{ date }}
							</a>
						</el-tooltip>

						<el-dropdown size="mini"
						             type="primary"
						             trigger="click"
						             :show-timeout="0"
						             :hide-timeout="0">
							<i class="el-icon-more-outline"></i>

							<el-dropdown-menu slot="dropdown">
								<el-dropdown-item v-if="!owns"
								                  @click.native="report">
									Report
								</el-dropdown-item>

								<el-dropdown-item class="go-red"
								                  @click.native="destroy"
								                  v-if="owns">
									Delete
								</el-dropdown-item>

								<el-dropdown-item class="go-green"
								                  @click.native="approve"
								                  v-if="showApprove"
								                  divided>
									Approve
								</el-dropdown-item>

								<el-dropdown-item class="go-red"
								                  @click.native="disapprove"
								                  v-if="showDisapprove">
									Delete
								</el-dropdown-item>
							</el-dropdown-menu>
						</el-dropdown>
					</div>
				</div>

				<div class="text">
					<markdown :text="list.content.text"></markdown>
				</div>
			</div>

			<div class="comments"
			     v-if="list.children.length">
				<comment :list="c"
				         v-for="c in sortedComments"
				         :key="c.id"
				         :full="full"></comment>
			</div>

			<el-button type="text"
			           v-if="hasMoreCommentsToLoad"
			           @click="loadMoreComments">
				Load More Comments ({{ list.children.length - childrenLimit }} more replies)
			</el-button>
		</div>
	</transition>
</template>


<script>
import Markdown from '../components/Markdown.vue';
import Helpers from '../mixins/Helpers';

export default {
    name: 'comment',

    props: ['list', 'comments-order', 'full'],

    components: {
        Markdown
    },

    mixins: [Helpers],

    data() {
        return {
            editing: false,
            body: this.list.content.text,
            visible: true,
            reply: false,
            childrenLimit: 4,
            highlighted: false
        };
    },

    created() {
        if (_.isUndefined(this.list.children)) {
            this.list.children = [];
        }

        this.$eventHub.$on('newComment', this.newComment);
        this.$eventHub.$on('patchedComment', this.patchedComment);
        this.$eventHub.$on('deletedComment', this.deletedComment);
    },

    beforeDestroy() {
        this.$eventHub.$off('newComment', this.newComment);
        this.$eventHub.$off('patchedComment', this.patchedComment);
        this.$eventHub.$off('deletedComment', this.deletedComment);
    },

    mounted() {
        this.$nextTick(function() {
            this.setHighlighted();
            this.scrollToComment();
        });
    },

    computed: {
        url() {},

        liked: {
            get() {
                try {
                    return Store.state.comments.likes.indexOf(this.list.id) !== -1 ? true : false;
                } catch (error) {
                    return false;
                }
            },

            set() {
                if (this.liked) {
                    this.list.likes_count--;
                    let index = Store.state.comments.likes.indexOf(this.list.id);
                    Store.state.comments.likes.splice(index, 1);

                    return;
                }

                this.list.likes_count++;
                Store.state.comments.likes.push(this.list.id);
            }
        },

        bookmarked: {
            get() {
                return Store.state.bookmarks.comments.indexOf(this.list.id) !== -1 ? true : false;
            },

            set() {
                if (Store.state.bookmarks.comments.indexOf(this.list.id) !== -1) {
                    let index = Store.state.bookmarks.comments.indexOf(this.list.id);
                    Store.state.bookmarks.comments.splice(index, 1);

                    return;
                }

                Store.state.bookmarks.comments.push(this.list.id);
            }
        },

        isParent() {
            return this.list.parent_id == null ? true : false;
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
            return this.list.likes_count;
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
            return _.orderBy(this.uniqueList, this.commentsOrder, 'desc').slice(0, this.childrenLimit);
        },

        /**
         * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
         * timestamps) we need a different approch to make sure the list is always unique.
         * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
         *
         * @return object
         */
        uniqueList() {
            let unique = [];
            let temp = [];

            this.list.children.forEach(function(element, index, self) {
                if (temp.indexOf(element.id) === -1) {
                    unique.push(element);
                    temp.push(element.id);
                }
            });

            return unique;
        },

        date() {
            return moment(this.list.created_at)
                .utc(moment().format('Z'))
                .fromNow(true);
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
            return (
                !this.list.approved_at &&
                (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 || meta.isVotenAdministrator) &&
                !this.owns
            );
        },

        /**
         * whether or not the disapprove button shoud be displayed
         *
         * @return boolean
         */
        showDisapprove() {
            return (
                !this.list.disapproved_at &&
                (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 || meta.isVotenAdministrator) &&
                !this.owns
            );
        }
    },

    methods: {
        openOrigin() {
            app.$Progress.start();

            axios
                .get(`/submissions/${this.list.submission_id}`)
                .then(response => {
                    this.$router.push(this.submissionUrl(response.data.data));

                    app.$Progress.finish();
                })
                .catch(error => {
                    app.$Progress.fail();
                });
        },

        doubleClicked() {
            if (this.isGuest) return;

            if (!this.liked) {
                this.like();
            }
        },

        /**
         * Sets the initial values for whether or not highlight the comment.
         *
         * @return void
         */
        setHighlighted() {
            if (this.list.broadcasted == true || this.$route.query.comment == this.list.id) {
                this.highlighted = true;
            }
        },

        /**
         * Scrolls the page to the comment
         *
         * @return void
         */
        scrollToComment() {
            if (this.$route.query.comment == this.list.id) {
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

        /**
         * Send record to be fetched by CommentForm.
         *
         * @return void
         */
        edit() {
            this.editing = !this.editing;

            this.$eventHub.$emit('edit-comment', this.list);
        },

        newComment(comment) {
            if (comment.parent_id == null) return;
            if (this.list.id != comment.parent_id) return;

            // owns the comment
            if (comment.author.id == auth.id) {
                this.reply = false;
                Store.state.comments.likes.push(comment.id);
                this.list.children.unshift(comment);

                this.$nextTick(function() {
                    document.getElementById('comment' + comment.id).scrollIntoView();
                });

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

            this.editing = false;
            this.list.content.text = comment.content.text;
            this.list.edited_at = this.now();
        },

        /**
         *  Report(and block) comment
         */
        report() {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            Store.modals.reportComment.show = true;
            Store.modals.reportComment.comment = this.list;
        },

        /**
         *  Send comment to CommentForm to be replied to.
         *
         * @return void
         */
        commentReply() {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            this.reply = !this.reply;

            this.$eventHub.$emit('reply-comment', this.list);
        },

        bookmark: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.bookmarked = !this.bookmarked;

                axios.post(`/comments/${this.list.id}/bookmark`).catch(() => {
                    this.bookmarked = !this.bookmarked;
                });
            },
            200,
            { leading: true, trailing: false }
        ),

        like: _.debounce(
            function() {
                if (this.isGuest) {
                    this.mustBeLogin();
                    return;
                }

                this.liked = !this.liked;

                axios.post(`/comments/${this.list.id}/like`).catch(error => {
                    this.liked = !this.liked;
                });
            },
            200,
            { leading: true, trailing: false }
        ),

        /**
         * Deletes the comment. Only the author is allowed to make such decision.
         *
         * @return void
         */
        destroy() {
            this.visible = false;

            axios.delete(`/comments/${this.list.id}`).catch(() => (this.visible = true));
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
         * Approves the comment. Only the moderators of channel are allowed to do this.
         *
         * @return void
         */
        approve() {
            this.list.approved_at = this.now();
            axios.post(`/comments/${this.list.id}/approve`).catch(() => (this.list.approved_at = null));
        },

        /**
         * Disapproves the comment. Only the moderators of channel are allowed to do this.
         *
         * @return void
         */
        disapprove() {
            this.visible = false;
            axios.post(`/comments/${this.list.id}/disapprove`).catch(() => (this.visible = true));
        }
    }
};
</script>
