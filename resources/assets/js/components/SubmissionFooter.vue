<template>
	<div class="index-submission-footer user-select">
		<!-- <span v-if="! isMobile">
			Submitted {{ date }} by
			<router-link :to="'/' + '@' + submission.author.username"
			             class="h-underline">
				{{ '@' + submission.author.username }}
			</router-link>
			to
			<router-link :to="'/c/' + submission.channel_name"
			             class="channel-label h-underline">#{{ submission.channel_name }}</router-link>
		</span> -->

		<!-- <div class="display-inline">
			<div class="display-inline">
				<el-tooltip class="item"
				            content="Comments"
				            placement="top"
				            transition="false"
				            :open-delay="500">
					<router-link :to="url"
					             class="comments-icon h-green">
						<i class="v-icon v-comment"></i>
						<span v-if="comments"
						      class="count"
						      v-text="comments"></span>
					</router-link>
				</el-tooltip>

				<el-tooltip class="item"
				            :content="bookmarked ? 'Unbookmark' : 'Bookmark'"
				            placement="top"
				            transition="false"
				            :open-delay="500">
					<a @click="$emit('bookmark')">
						<i class="v-icon h-yellow pointer"
						   :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
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
						                  @click.native="$emit('report')">
							Report
						</el-dropdown-item>

						<el-dropdown-item @click.native="$emit('hide')"
						                  v-if="!owns">
							Hide
						</el-dropdown-item>

						<el-dropdown-item @click.native="$emit('nsfw')"
						                  v-if="showNSFW">
							NSFW
						</el-dropdown-item>

						<el-dropdown-item @click.native="$emit('sfw')"
						                  v-if="showSFW">
							Family Safe
						</el-dropdown-item>

						<el-dropdown-item class="go-red"
						                  @click.native="$emit('destroy')"
						                  v-if="owns">
							Delete
						</el-dropdown-item>

						<el-dropdown-item class="go-green"
						                  @click.native="$emit('approve')"
						                  v-if="showApprove"
						                  divided>
							Approve
						</el-dropdown-item>

						<el-dropdown-item class="go-red"
						                  @click.native="$emit('disapprove')"
						                  v-if="showDisapprove">
							Delete
						</el-dropdown-item>
					</el-dropdown-menu>
				</el-dropdown>
			</div>
		</div> -->

		<div>
			<el-tooltip class="item"
			            content="Comments"
			            placement="top"
			            transition="false"
			            :open-delay="500">
				<router-link :to="url"
				             class="comments-icon h-green">
					<i class="v-icon v-comment"></i>
					<span v-if="comments"
					      class="count"
					      v-text="comments"></span>
				</router-link>
			</el-tooltip>

			<el-tooltip class="item"
			            :content="bookmarked ? 'Unbookmark' : 'Bookmark'"
			            placement="top"
			            transition="false"
			            :open-delay="500">
				<a @click="$emit('bookmark')">
					<i class="v-icon h-yellow pointer"
					   :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
				</a>
			</el-tooltip>

			<el-dropdown size="mini"
			             type="primary"
						 class="margin-left-1"
			             trigger="click"
			             :show-timeout="0"
			             :hide-timeout="0">
				<i class="el-icon-more-outline"></i>

				<el-dropdown-menu slot="dropdown">
					<el-dropdown-item v-if="!owns"
					                  @click.native="$emit('report')">
						Report
					</el-dropdown-item>

					<el-dropdown-item @click.native="$emit('hide')"
					                  v-if="!owns">
						Hide
					</el-dropdown-item>

					<el-dropdown-item @click.native="$emit('nsfw')"
					                  v-if="showNSFW">
						NSFW
					</el-dropdown-item>

					<el-dropdown-item @click.native="$emit('sfw')"
					                  v-if="showSFW">
						Family Safe
					</el-dropdown-item>

					<el-dropdown-item class="go-red"
					                  @click.native="$emit('destroy')"
					                  v-if="owns">
						Delete
					</el-dropdown-item>

					<el-dropdown-item class="go-green"
					                  @click.native="$emit('approve')"
					                  v-if="showApprove"
					                  divided>
						Approve
					</el-dropdown-item>

					<el-dropdown-item class="go-red"
					                  @click.native="$emit('disapprove')"
					                  v-if="showDisapprove">
						Delete
					</el-dropdown-item>
				</el-dropdown-menu>
			</el-dropdown>
		</div>
	</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['url', 'comments', 'bookmarked', 'submission', 'liked', 'points'],

    computed: {
        /**
         * Does the auth user own the submission
         *
         * @return Boolean
         */
        owns() {
            return auth.id == this.submission.author.id;
        },

        showApprove() {
            try {
                return (
                    !this.submission.approved_at &&
                    (Store.state.moderatingAt.indexOf(this.submission.channel_id) != -1 || meta.isVotenAdministrator) &&
                    !this.owns
                );
            } catch (error) {
                return false;
            }
        },

        showDisapprove() {
            try {
                return (
                    !this.submission.disapproved_at &&
                    (Store.state.moderatingAt.indexOf(this.submission.channel_id) != -1 || meta.isVotenAdministrator) &&
                    !this.owns
                );
            } catch (error) {
                return false;
            }
        },

        showNSFW() {
            try {
                return (
                    (this.owns ||
                        Store.state.moderatingAt.indexOf(this.submission.channel_id) != -1 ||
                        meta.isVotenAdministrator) &&
                    !this.submission.nsfw
                );
            } catch (error) {
                return false;
            }
        },

        showSFW() {
            try {
                return (
                    (this.owns ||
                        Store.state.moderatingAt.indexOf(this.submission.channel_id) != -1 ||
                        meta.isVotenAdministrator) &&
                    this.submission.nsfw
                );
            } catch (error) {
                return false;
            }
        },

        showRemoveTumbnail() {
            return this.owns && this.submission.content.thumbnail ? true : false;
        },

        date() {
            return moment(this.submission.created_at)
                .utc(moment().format('Z'))
                .fromNow();
        }
    }
};
</script>
