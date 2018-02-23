<template>
	<transition name="el-fade-in-linear">
		<div class="submission-item submission-wrapper"
            @dblclick="doubleClicked"
		     v-show="!hidden"
		     :id="'submission' + list.id">
			<!-- side-voting -->
			<div class="side-voting desktop-only">
				<i class="v-icon v-up-fat side-vote-icon"
				   :class="upvoted ? 'go-primary animated bounceIn' : 'go-gray'"
				   @click="voteUp"></i>

				<div class="user-select vote-number">
					{{ points }}
				</div>

				<i class="v-icon v-down-fat side-vote-icon"
				   @click="voteDown"
				   :class="downvoted ? 'go-red animated bounceIn' : 'go-gray'"></i>
			</div>

			<article class="flex1"
			         v-bind:class="'box-typical profile-post ' + list.type">
				<!-- content -->
				<div class="profile-post-content">
					<text-submission v-if="list.type == 'text'"
					                 :submission="list"
					                 :nsfw="nsfw"
					                 :full="full"
					                 @bookmark="bookmark"
					                 :url="'/c/' + list.channel_name + '/' + list.slug"
					                 :comments="list.comments_count"
					                 :bookmarked="bookmarked"
					                 @report="report"
					                 @hide="hide"
					                 @nsfw="markAsNSFW"
					                 @sfw="markAsSFW"
					                 @destroy="destroy"
					                 @approve="approve"
					                 @disapprove="disapprove"
					                 @removethumbnail="removeThumbnail"
					                 :upvoted="upvoted"
					                 :downvoted="downvoted"
					                 @upvote="voteUp"
					                 @downvote="voteDown"
					                 :points="points"></text-submission>

					<img-submission v-if="list.type == 'img'"
					                :submission="list"
					                :nsfw="nsfw"
					                :full="full"
					                @zoom="showPhotoViewer"
					                @bookmark="bookmark"
					                :url="'/c/' + list.channel_name + '/' + list.slug"
					                :comments="list.comments_count"
					                :bookmarked="bookmarked"
					                @report="report"
					                @hide="hide"
					                @nsfw="markAsNSFW"
					                @sfw="markAsSFW"
					                @destroy="destroy"
					                @approve="approve"
					                @disapprove="disapprove"
					                @removethumbnail="removeThumbnail"
					                :upvoted="upvoted"
					                :downvoted="downvoted"
					                @upvote="voteUp"
					                @downvote="voteDown"
					                :points="points"></img-submission>

					<gif-submission v-if="list.type == 'gif'"
					                :submission="list"
					                :nsfw="nsfw"
					                :full="full"
					                @bookmark="bookmark"
					                @play-gif="showGifPlayer"
					                :url="'/c/' + list.channel_name + '/' + list.slug"
					                :comments="list.comments_count"
					                :bookmarked="bookmarked"
					                @report="report"
					                @hide="hide"
					                @nsfw="markAsNSFW"
					                @sfw="markAsSFW"
					                @destroy="destroy"
					                @approve="approve"
					                @disapprove="disapprove"
					                @removethumbnail="removeThumbnail"
					                :upvoted="upvoted"
					                :downvoted="downvoted"
					                @upvote="voteUp"
					                @downvote="voteDown"
					                :points="points"></gif-submission>

					<link-submission v-if="list.type == 'link'"
					                 :submission="list"
					                 :nsfw="nsfw"
					                 :full="full"
					                 @embed="showEmbed"
					                 @bookmark="bookmark"
					                 :url="'/c/' + list.channel_name + '/' + list.slug"
					                 :comments="list.comments_count"
					                 :bookmarked="bookmarked"
					                 @report="report"
					                 @hide="hide"
					                 @nsfw="markAsNSFW"
					                 @sfw="markAsSFW"
					                 @destroy="destroy"
					                 @approve="approve"
					                 @disapprove="disapprove"
					                 @removethumbnail="removeThumbnail"
					                 :upvoted="upvoted"
					                 :downvoted="downvoted"
					                 @upvote="voteUp"
					                 @downvote="voteDown"
					                 :points="points"></link-submission>
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

    methods: {
        doubleClicked() {
            if (this.isGuest) return;

            if (!this.currentVote) {
                this.voteUp();
            }
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

            this.hidden = true;

            axios
                .post('/hide-submission', {
                    submission_id: this.list.id
                })
                .catch(() => {
                    this.hidden = false;
                });
        },

        /**
         * Deletes the submission. Only the author is allowed to make such decision.
         *
         * @return void
         */
        destroy() {
            this.hidden = true;

            axios.delete(`/submissions/${this.list.id}`).catch(() => {
                this.hidden = false;
            });
        },

        /**
         * Disapproves the submission. Only the moderators of channel are allowed to do this.
         *
         * @return void
         */
        disapprove() {
            this.hidden = true;

            axios
                .post('/disapprove-submission', { submission_id: this.list.id })
                .catch(() => (this.hidden = false));
        }
    }
};
</script>
