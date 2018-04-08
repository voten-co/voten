<template>
	<div id="submission-page"
	     class="home-wrapper user-select">
		<div class="flex1"
		     id="comments-submission-page">
			<submission-channel-header></submission-channel-header>

			<div class="col-full padding-bottom-1 flex1">
				<nsfw-warning v-if="submission.nsfw == 1 && !Store.settings.feed.include_nsfw_submissions"
				              :text="'This submission contains NSFW content which can not be displayed according to your personal settings.'">
				</nsfw-warning>

				<div v-if="submission.nsfw == 0 || Store.settings.feed.include_nsfw_submissions">
					<loading v-if="loadingSubmission"></loading>

					<full-submission v-if="!loadingSubmission"
					                 :list="submission"
					                 :full="true"></full-submission>

					<section class="box-typical comments"
					         id="comments-section"
					         v-if="!loadingSubmission">
						<header class="user-select flex-space">
							<div class="v-bold">
								<span v-show="comments.length">{{ submission.comments_count }}</span> Comments:
								<span class="go-gray go-small"
								      v-if="!isGuest">({{ onlineUsersCount }} online users)</span>
							</div>

							<el-dropdown size="medium"
							             trigger="click"
							             :show-timeout="0"
							             :hide-timeout="0"
							             type="primary"
							             v-show="comments.length > 1">
								<span class="el-dropdown-link">
									{{ sort === 'hot' ? 'Hot' : 'New' }}
									<i class="el-icon-arrow-down el-icon--right"></i>
								</span>

								<el-dropdown-menu slot="dropdown">
									<el-dropdown-item @click.native="newSort('hot')">
										Hot
									</el-dropdown-item>

									<el-dropdown-item @click.native="newSort('new')">
										New
									</el-dropdown-item>
								</el-dropdown-menu>
							</el-dropdown>
						</header>

						<div class="box-typical-inner ui threaded comments"
						     v-if="submission.id != 0">
							<span class="simple-loading"
							      v-if="loadingComments && page < 2">
								<i class="el-icon-loading"></i>
							</span>

							<span v-if="!loadingComments && comments.length < 1"
							      class="no-comments-yet">
								No comments here yet. Care to be the first one?
							</span>

							<comment :list="c"
							         :comments-order="commentsOrder"
							         v-for="c in uniqueList"
							         :key="c.id"
							         :full="true"></comment>
						</div>
					</section>

					<div class="align-center margin-bottom-1"
					     v-if="moreComments">
						<el-button round
						           plain
						           class="half-width margin-top-1"
						           @click="loadMoreComments"
						           :loading="loadingComments">
							Load More Comments
						</el-button>
					</div>
				</div>
			</div>
		</div>

		<comment-form :submission="submission.id"
		              :commentors="commentors"></comment-form>
	</div>
</template>

<script>
import FullSubmission from '../components/FullSubmission.vue';
import Comment from '../components/Comment.vue';
import CommentForm from '../components/CommentForm.vue';
import ChannelHeader from '../components/ChannelHeader.vue';
import SubmissionChannelHeader from '../components/SubmissionChannelHeader.vue';
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
        ChannelHeader,
        SubmissionChannelHeader,
        NsfwWarning
    },

    data() {
        return {
            page: 1,
            moreComments: false,
            loadingComments: true,
            comments: [],
            sort: 'hot',
            onlineUsers: []
        };
    },

    created() {
        this.getComments();
        this.listen();
        this.$eventHub.$on('newComment', this.newComment);
        this.setPageTitle(this.submission.title);
    },

    beforeDestroy() {
        this.$eventHub.$off('newComment', this.newComment);
    },

    watch: {
        $route() {
            this.clear();
            this.getComments();
            this.listen();
            this.$eventHub.$on('newComment', this.newComment);
            this.setPageTitle(this.submission.title);
        }
    },

    beforeRouteEnter(to, from, next) {
        if (
            typeof Store.page.channel.temp.name != 'undefined' &&
            Store.page.channel.temp.name != to.params.name
        ) {
            Store.page.submission.clearSubmission();
        }

        if (typeof app != 'undefined') {
            app.$Progress.start();
        }

        Store.page.submission
            .getSubmission(to.params.slug)
            .then(() => {
                next((vm) => {
                    vm.$Progress.finish();
                });
            })
            .catch((error) => {
                // if (error.response.status === 404) {
                // 	this.$router.push('/404')
                // }
            });
    },

    beforeRouteLeave(to, from, next) {
        Echo.leave('submission.' + from.params.slug);

        next();
    },

    beforeRouteUpdate(to, from, next) {
        if (to.hash !== from.hash) return;

        Store.page.submission.clearSubmission();
        this.$Progress.start();

        Store.page.submission
            .getSubmission(to.params.slug)
            .then(() => {
                Echo.leave('submission.' + from.params.slug);
                this.$Progress.finish();

                next();
            })
            .catch((error) => {
                // if (error.response.status === 404) {
                // 	this.$router.push('/404')
                // }

                this.$Progress.fail();
            });
    },

    computed: {
        commentors() {
            return _.map(this.comments, 'author');
        },

        submission() {
            return Store.page.submission.submission;
        },

        loadingSubmission() {
            return Store.page.submission.loadingSubmission;
        },

        onlineUsersCount() {
            return this.onlineUsers.length;
        },

        uniqueList() {
            let unique = [];
            let temp = [];

            this.comments.forEach(function(element, index, self) {
                if (temp.indexOf(element.id) === -1) {
                    unique.push(element);
                    temp.push(element.id);
                }
            });

            return unique;
        },

        /**
         * The order that comments should be printed with
         *
         * @return string
         */
        commentsOrder() {
            return this.sort == 'hot' ? 'rate' : 'created_at';
        }
    },

    methods: {
        clear() {
            this.moreComments = false;
            this.page = 1;
            this.comments = [];
        },

        loadMoreComments() {
            this.page++;
            this.getComments();
        },

        /**
         * receives the broadcasted comment.
         *
         * @return void
         */
        newComment(comment) {
            if (
                comment.parent_id != null ||
                comment.submission_id != this.submission.id
            )
                return;

            // add broadcasted (used for styling)
            if (comment.user_id != auth.id) {
                comment.broadcasted = true;
            }

            this.comments.unshift(comment);
            this.submission.comments_count++;

            if (comment.user_id == auth.id) {
                this.$nextTick(function() {
                    document
                        .getElementById('comment' + comment.id)
                        .scrollIntoView();
                });
            }
        },

        /**
         * listen for broadcasted comments
         *
         * @return void
         */
        listen() {
            const channelAddress = 'submission.' + this.$route.params.slug;

            Echo.channel(channelAddress)
                .listen('CommentWasCreated', (event) => {
                    this.$eventHub.$emit('newComment', event.data);
                })
                .listen('CommentWasPatched', (event) => {
                    this.$eventHub.$emit('patchedComment', event.data);
                })
                .listen('CommentWasDeleted', (event) => {
                    this.$eventHub.$emit('deletedComment', event.data);
                });

            // we can't do presence channel or/and listen for private channels, if the user is a guest
            if (this.isGuest) return;

            Echo.join(channelAddress)
                .here((users) => {
                    this.onlineUsers = users;
                })
                .joining((user) => {
                    this.onlineUsers.push(user);
                })
                .leaving((user) => {
                    let index = this.onlineUsers.indexOf(user.username);
                    this.onlineUsers.splice(index, 1);

                    // if typer loses his connection for any reason, we $emit "finished-typing" because
                    // after all, we must make sure other users won't see "@user is typing" forever!
                    this.$eventHub.$emit('finished-typing', user.username);
                });
        },

        /**
         * get comments
         *
         * @return void
         */
        getComments() {
            this.loadingComments = true;

            axios
                .get(`/submissions/${this.submission.id}/comments`, {
                    params: {
                        page: this.page,
                        sort: this.sort,
                        with_children: true,
                        with_parent: true
                    }
                })
                .then((response) => {
                    this.loadingComments = false;

                    this.comments.push(...response.data.data);

                    if (response.data.links.next != null) {
                        this.moreComments = true;
                    } else {
                        this.moreComments = false;
                    }
                })
                .catch((error) => {
                    this.loadingComments = false;

                    this.$message({
                        message: `Something went wrong and we couldn't load comments. Try refreshing the page. `,
                        type: 'error'
                    });
                });
        },

        newSort(sort) {
            if (sort == this.sort) return;

            this.clear();
            this.getComments();
            this.sort = sort;
        }
    }
};
</script>
