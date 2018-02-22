<template>
	<div class="index-submission-footer user-select">
		<div class="display-inline">
			<div class="display-inline">
				<el-tooltip class="item" content="Comments" placement="top" transition="false" :open-delay="500">
					<router-link :to="url" class="comments-icon h-green">
						<i class="v-icon v-comment"></i><span v-if="comments" v-text="comments"></span>
					</router-link>
				</el-tooltip>
				
				<el-tooltip class="item" :content="bookmarked ? 'Unbookmark' : 'Bookmark'" placement="top" transition="false" :open-delay="500">
					<a @click="$emit('bookmark')">
						<i class="v-icon h-yellow pointer" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
					</a>
				</el-tooltip>
				
				<el-dropdown size="mini" type="primary" trigger="click" :show-timeout="0" :hide-timeout="0">
					<i class="el-icon-more-outline"></i>

					<el-dropdown-menu slot="dropdown">
						<el-dropdown-item v-if="!owns" @click.native="$emit('report')">
							Report
						</el-dropdown-item>
						
						<el-dropdown-item @click.native="$emit('hide')" v-if="!owns">
							Hide
						</el-dropdown-item>

						<el-dropdown-item @click.native="$emit('nsfw')" v-if="showNSFW">
							NSFW
						</el-dropdown-item>
						
						<el-dropdown-item @click.native="$emit('sfw')" v-if="showSFW">
							Family Safe
						</el-dropdown-item>

						<el-dropdown-item class="go-red" @click.native="$emit('destroy')" v-if="owns">
							Delete
						</el-dropdown-item>
						
						<el-dropdown-item class="go-green" @click.native="$emit('approve')" v-if="showApprove" divided>
							Approve
						</el-dropdown-item>
						
						<el-dropdown-item class="go-red" @click.native="$emit('disapprove')" v-if="showDisapprove">
							Delete
						</el-dropdown-item>
					</el-dropdown-menu>
				</el-dropdown>
			</div>

			<div class="voting-wrapper display-none mobile-only" v-if="isMobile">
				<a class="fa-stack align-right" @click="$emit('upvote')">
					<i class="v-icon v-up-fat" :class="upvoted ? 'go-primary animated bounceIn' : 'go-gray'"></i>
				</a>

				<div class="detail">
					{{ points }} Points
				</div>

				<a class="fa-stack align-right" @click="$emit('downvote')">
					<i class="v-icon v-down-fat" :class="downvoted ? 'go-red animated bounceIn' : 'go-gray'"></i>
				</a>
			</div>
		</div>

		<span class="desktop-only" v-if="! isMobile">
			Submitted {{ date }} by
			<router-link :to="'/' + '@' + submission.author.username" class="h-underline desktop-only">
				{{ '@' + submission.author.username }}
			</router-link>
			to <router-link :to="'/c/' + submission.channel_name" class="channel-label h-underline">#{{ submission.channel_name }}</router-link>
		</span>

		<div class="mobile-only mobile-submission-item-action" v-if="isMobile">
			{{ date }} by
			<router-link :to="'/' + '@' + submission.author.username" class="h-underline">
				{{ '@' + submission.author.username }}
			</router-link>
			to <router-link :to="'/c/' + submission.channel_name" class="channel-label h-underline">#{{ submission.channel_name }}</router-link>
		</div>
	</div>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: [
        'url',
        'comments',
        'bookmarked',
        'submission',
        'upvoted',
        'downvoted',
        'points'
    ],

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
            return (
                !this.submission.approved_at &&
                (Store.state.moderatingAt.indexOf(this.submission.channel_id) !=
                    -1 ||
                    meta.isVotenAdminstrator) &&
                !this.owns
            );
        },

        showDisapprove() {
            return (
                !this.submission.deleted_at &&
                (Store.state.moderatingAt.indexOf(this.submission.channel_id) !=
                    -1 ||
                    meta.isVotenAdminstrator) &&
                !this.owns
            );
        },

        showNSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(
                        this.submission.channel_id
                    ) != -1 ||
                    meta.isVotenAdminstrator) &&
                !this.submission.nsfw
            );
        },

        showSFW() {
            return (
                (this.owns ||
                    Store.state.moderatingAt.indexOf(
                        this.submission.channel_id
                    ) != -1 ||
                    meta.isVotenAdminstrator) &&
                this.submission.nsfw
            );
        },

        showRemoveTumbnail() {
            if (this.owns && this.submission.content.thumbnail) return true;
            return false;
        },

        date() {
            return moment(this.submission.created_at)
                .utc(moment().format('Z'))
                .fromNow();
        }
    }
};
</script>
