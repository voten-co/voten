<template>
	<transition name="fade">
		<div class="submission-wrapper"
		     v-show="!hidden">
			<article class="flex1"
			         v-bind:class="'box-typical profile-post ' + list.type">
				<!-- header -->
				<div class="submission-header user-select">
					<div class="submission-header-container">
						<div class="submission-submitter-wrapper">
							<router-link :to="'/' + '@' + list.author.username"
							             class="desktop-only">
								<img v-bind:src="list.author.avatar"
								     v-bind:alt="list.author.username"
								     class="submission-avatar">
							</router-link>

							<div class="submission-submitter">
								<router-link :to="'/' + '@' + list.author.username"
								             class="username">
									@{{ list.author.username }}
								</router-link>

								<el-tooltip :content="'Created: ' + longDate"
								            placement="bottom-start"
								            transition="false"
								            :open-delay="500">
									<span class="date">
										{{ date }}
									</span>
								</el-tooltip>
							</div>
						</div>

						<div class="flex-center">
							<div>
								<el-tooltip content="Edit"
								            placement="bottom"
								            transition="false"
								            :open-delay="500">
									<a class="reply"
									   v-if="owns && (list.type == 'text')"
									   @click="edit">
										<i class="v-icon v-edit go-gray h-purple pointer"></i>
									</a>
								</el-tooltip>
							</div>

							<div class="voting-wrapper display-none">
								<a class="fa-stack align-right"
								   @click="voteUp">
									<i class="v-icon v-up-fat"
									   :class="upvoted ? 'go-primary animated bounceIn' : 'go-gray'"></i>
								</a>

								<el-tooltip :content="detailedPoints"
								            placement="bottom"
								            transition="false"
								            :open-delay="500">
									<div class="detail">
										{{ points }} Points
									</div>
								</el-tooltip>

								<a class="fa-stack align-right"
								   @click="voteDown">
									<i class="v-icon v-down-fat"
									   :class="downvoted ? 'go-red animated bounceIn' : 'go-gray'"></i>
								</a>
							</div>

							<div class="margin-left-1">
								<el-dropdown size="medium"
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

										<el-dropdown-item @click.native="hide"
										                  v-if="!owns">
											Hide
										</el-dropdown-item>

										<el-dropdown-item @click.native="markAsNSFW"
										                  v-if="showNSFW">
											NSFW
										</el-dropdown-item>

										<el-dropdown-item @click.native="markAsSFW"
										                  v-if="showSFW">
											Family Safe
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

										<el-dropdown-item @click.native="removeThumbnail"
										                  v-if="showRemoveTumbnail">
											Remove Thumbnail
										</el-dropdown-item>
									</el-dropdown-menu>
								</el-dropdown>

								<el-tooltip :content="bookmarked ? 'Unbookmark' : 'Bookmark'"
								            placement="bottom-end"
								            transition="false"
								            :open-delay="500">
									<a class="fa-stack"
									   @click="bookmark">
										<i class="v-icon h-yellow"
										   :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
									</a>
								</el-tooltip>
							</div>
						</div>
					</div>
				</div>

				<!-- content -->
				<div class="profile-post-content">
					<text-submission v-if="list.type == 'text'"
					                 :submission="list"
					                 :nsfw="nsfw"
					                 :full="full"></text-submission>

					<img-submission v-if="list.type == 'img'"
					                :submission="list"
					                :nsfw="nsfw"
					                :full="full"
					                @zoom="showPhotoViewer"></img-submission>

					<gif-submission v-if="list.type == 'gif'"
					                :submission="list"
					                :nsfw="nsfw"
					                :full="full"
					                @play-gif="showGifPlayer"></gif-submission>

					<link-submission v-if="list.type == 'link'"
					                 :submission="list"
					                 :nsfw="nsfw"
					                 :full="full"
					                 @embed="showEmbed"></link-submission>
				</div>
			</article>
		</div>
	</transition>
</template>

<script>
import Helpers from '../mixins/Helpers';
import Submission from '../mixins/Submission';

export default {
    mixins: [Helpers, Submission],

    computed: {
        detailedPoints() {
            return `+${this.list.upvotes_count} | -${
                this.list.downvotes_count
            }`;
        },

        showApprove() {
            return (
                !this.list.approved_at &&
                (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 ||
                    meta.isVotenAdminstrator) &&
                !this.owns
            );
        },

        showDisapprove() {
            return (
                !this.list.deleted_at &&
                (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 ||
                    meta.isVotenAdminstrator) &&
                !this.owns
            );
        },

        showNSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(this.list.channel_id) !=
                        -1 ||
                    meta.isVotenAdminstrator) &&
                !this.list.nsfw
            );
        },

        showSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(this.list.channel_id) !=
                        -1 ||
                    meta.isVotenAdminstrator) &&
                this.list.nsfw
            );
        },

        showRemoveTumbnail() {
            return this.owns && this.list.content.thumbnail ? true : false;
        },

        /**
         * Calculates the long date to display for hover over date.
         *
         * @return String
         */
        longDate() {
            return this.parseFullDate(this.list.created_at);
        }
    },

    methods: {
        /**
         * Fires the "submission-edit" event that gets picked up by the TextSubmission.vue component.
         *
         * @return void
         */
        edit() {
            this.$eventHub.$emit('edit-submission');
        },

        /**
         * hide(block) submission
         *
         * @return void
         */
        hide() {
            if (this.isGuest) {
                this.mustBeLogin();
                return;
            }

            axios
                .post('/hide-submission', {
                    submission_id: this.list.id
                })
                .then(() => {
                    this.$message({
                        message:
                            'You will no longer see this post in your feed.',
                        type: 'success'
                    });
                });

            history.go(-1);
        },

        /**
         * Deletes the submission. Only the author is allowed to make such decision.
         *
         * @return void
         */
        destroy() {
            axios.delete(`/submissions/${this.list.id}`).then(() => {
                this.$message({
                    message: 'Post was successfully deleted.',
                    type: 'success'
                });
            });

            history.go(-1);
        },

        /**
         * Disapproves the submission. Only the moderators of channel are allowed to do this.
         *
         * @return void
         */
        disapprove() {
            axios
                .post('/disapprove-submission', {
                    submission_id: this.list.id
                })
                .then(() => {
                    this.$message({
                        message: 'Post was successfully deleted.',
                        type: 'success'
                    });
                });

            if (this.full) {
                history.go(-1);
            } else {
                this.hidden = true;
            }
        }
    }
};
</script>
