<template>
	<div id="submission-page" class="home-wrapper">
		<div class="flex1" id="comments-submission-page">
			<submission-category-header></submission-category-header>
	
			<div class="col-full padding-bottom-1 flex1">
				<nsfw-warning v-if="submission.nsfw == 1 && !auth.nsfw" :text="'This submission contains NSFW content which can not be displayed according to your personal settings.'">
				</nsfw-warning>
	
				<div v-if="submission.nsfw == 0 || auth.nsfw">
					<loading v-if="loadingSubmission"></loading>
	
					<full-submission v-if="!loadingSubmission" :list="submission" :full="true"></full-submission>
	
					<section class="box-typical comments" id="comments-section" v-if="!loadingSubmission">
						<header class="user-select flex-space">
							<div class="v-bold">
								<span v-show="comments.length">{{ submission.comments_number }}</span> Comments: <span class="go-gray go-small" v-if="!isGuest">({{ onlineUsersCount }} online users)</span>
							</div>
	
							<el-dropdown size="medium" trigger="click" :show-timeout="0" :hide-timeout="0" type="primary" v-show="comments.length > 1">
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
	
						<div class="box-typical-inner ui threaded comments" v-if="submission.id != 0">
							<span class="simple-loading" v-if="loadingComments && page < 2">
																<i class="el-icon-loading"></i>
															</span>
	
							<span v-if="!loadingComments && comments.length < 1" class="no-comments-yet">
																No comments here yet. Care to be the first one?
															</span>
	
							<comment :list="c" :comments-order="commentsOrder" v-for="c in uniqueList" :key="c.id" :full="true"></comment>
						</div>
					</section>
	
					<div class="align-center" v-if="moreComments">
						<el-button type="success" plain class="half-width" @click="loadMoreComments" :loading="loadingComments">
							Load More Comments
						</el-button>
					</div>
				</div>
			</div>
		</div>
	
		<comment-form :submission="submission.id"></comment-form>
	</div>
</template>

<script>
	import FullSubmission from '../components/FullSubmission.vue';
	import Comment from '../components/Comment.vue';
	import CommentForm from '../components/CommentForm.vue';
	import CategoryHeader from '../components/CategoryHeader.vue';
	import SubmissionCategoryHeader from '../components/SubmissionCategoryHeader.vue';
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
			SubmissionCategoryHeader,
			NsfwWarning
		},
	
		data() {
			return {
				page: 1,
				moreComments: false,
				loadingComments: true,
				comments: [],
				sort: 'hot',
				onlineUsers: [],
				preload
			}
		},
	
		created() {
			this.getComments();
			this.listen();
			this.$eventHub.$on('newComment', this.newComment);
			this.setPageTitle(this.submission.title);
		},
	
		watch: {
			'$route' () {
				this.clear();
				this.getComments();
				this.listen();
				this.$eventHub.$on('newComment', this.newComment);
				this.setPageTitle(this.submission.title);
			}
		},
	
		beforeRouteEnter(to, from, next) {
			if (typeof Store.page.category.temp.name != 'undefined' && Store.page.category.temp.name != to.params.name) {
				Store.page.submission.clearSubmission();
			}
	
			if (typeof app != "undefined") {
				app.$Progress.start();
			}
	
			Store.page.submission.getSubmission(to.params.slug).then(() => {
				next(vm => {
					vm.$Progress.finish();
				});
			}).catch((error) => {
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
			Store.page.submission.clearSubmission();
			this.$Progress.start();
	
			Store.page.submission.getSubmission(to.params.slug).then(() => {
				Echo.leave('submission.' + from.params.slug);
				this.$Progress.finish();
	
				next();
			}).catch((error) => {
				// if (error.response.status === 404) {
				// 	this.$router.push('/404')
				// }
	
				this.$Progress.fail();
			});
	
	
		},
	
		computed: {
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
			 * The order that comments should be printed with
			 *
			 * @return string
			 */
			commentsOrder() {
				return this.sort == 'hot' ? 'rate' : 'created_at';
			},
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
				if (comment.parent_id != 0 || comment.submission_id != this.submission.id) return;
	
				// add broadcasted (used for styling)
				if (comment.user_id != auth.id) {
					comment.broadcasted = true;
				}
	
				this.comments.unshift(comment);
				this.submission.comments_number++;
			},
	
			/**
			 * listen for broadcasted comments
			 *
			 * @return void
			 */
			listen() {
				const channelAddress = 'submission.' + this.$route.params.slug;
	
				Echo.channel(channelAddress)
					.listen('CommentCreated', event => {
						this.$eventHub.$emit('newComment', event.comment);
					}).listen('CommentWasPatched', event => {
						this.$eventHub.$emit('patchedComment', event.comment);
					}).listen('CommentWasDeleted', event => {
						this.$eventHub.$emit('deletedComment', event.comment);
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
				this.loadingComments = true
	
				axios.get('/submission-comments', {
					params: {
						submission_slug: this.$route.params.slug,
						page: this.page,
						sort: this.sort
					}
				}).then((response) => {
					this.loadingComments = false;
	
					this.comments.push(...response.data.data);
	
					if (response.data.next_page_url != null) {
						this.moreComments = true;
					} else {
						this.moreComments = false;
					}
				})
			},
	
			newSort(sort) {
				if (sort == this.sort) return;
	
				Store.page.submission.clear();
				this.getComments();
				this.sort = sort;
			}
		}
	}
</script>
