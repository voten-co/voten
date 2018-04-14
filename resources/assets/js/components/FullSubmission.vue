<style lang="scss">
.submission-full {
    .actions {
        a {
            font-size: 13px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            color: #4c4b4b;
            cursor: pointer;
        }

        i {
            color: #657786;
            font-size: 17px;
            margin-left: 1em;
        }

        .count {
            margin-left: 0.5em;
        }
    }
}
</style>


<template>
	<transition name="fade">
		<div class="submission-wrapper submission-full">
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

						<div class="actions flex-center">
							<div>
								<!-- <a class="reply"
								   v-if="owns && (list.type == 'text')"
								   @click="edit">
									<el-tooltip content="Edit"
									            placement="bottom"
									            transition="false"
									            :open-delay="500">
										<i class="v-icon v-edit go-gray h-purple pointer"></i>
									</el-tooltip>
								</a> -->
								<el-button size="mini"
								           class=""
								           v-if="owns && (list.type == 'text')"
								           @click="edit"
								           round>
									Edit
								</el-button>
							</div>

							<a @click="like">
								<el-tooltip content="Like"
								            placement="bottom"
								            transition="false"
								            :open-delay="500">
									<i class="v-icon like-icon"
									   :class="liked ? 'v-heart-filled go-red animated bounceIn' : 'v-heart go-gray h-red'"></i>
								</el-tooltip>

								<span class="count"
								      v-text="points"></span>
							</a>

							<a class="fa-stack"
							   @click="bookmark">
								<el-tooltip :content="bookmarked ? 'Unbookmark' : 'Bookmark'"
								            placement="bottom-end"
								            transition="false"
								            :open-delay="500">
									<i class="v-icon h-yellow"
									   :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
								</el-tooltip>
							</a>

							<div class="margin-right-1">
								<el-dropdown size="medium"
								             type="primary"
								             trigger="click"
								             :show-timeout="0"
								             :hide-timeout="0">
									<i class="el-icon-more-outline h-black"></i>

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
							</div>
						</div>
					</div>
				</div>

				<!-- content -->
				<div class="profile-post-content full-page">
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
        showApprove() {
            return (
                !this.list.approved_at &&
                (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 || meta.isVotenAdministrator) &&
                !this.owns
            );
        },

        showDisapprove() {
            return (
                !this.list.disapproved_at &&
                (Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 || meta.isVotenAdministrator) &&
                !this.owns
            );
        },

        showNSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 ||
                    meta.isVotenAdministrator) &&
                !this.list.nsfw
            );
        },

        showSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(this.list.channel_id) != -1 ||
                    meta.isVotenAdministrator) &&
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

            axios.post(`/submissions/${this.list.id}/hide`).then(() => {
                this.$message({
                    message: 'You will no longer see this post in your feed.',
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
                .post(`/submissions/${this.list.id}/disapprove`)
                .then(() => {
                    this.$message({
                        message: 'Post was successfully deleted.',
                        type: 'success'
                    });
                });

            history.go(-1);
        }
    }
};
</script>
