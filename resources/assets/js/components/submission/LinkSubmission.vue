<style lang="scss">
.submission-page-preview {
    iframe {
        width: 70em;
        max-width: 100%;
        height: 36em;
        max-height: 48%;
    }
}
</style>

<template>
	<div>
		<div v-if="showBigThumbnail && submission.content.thumbnail" class="submission-page-preview">
			<div v-html="submission.content.embed" class="video-player-wrapper" v-if="showEmbed"></div>

			<div v-else>
				<a :href="submission.content.url"
						target="_blank"
						rel="nofollow"
						v-if="submission.content.thumbnail">
					<img :src="submission.content.thumbnail"
									:alt="submission.title"
									class="big-thumbnail" />
				</a>
			</div>
		</div>

		<div class="link-list-info flex-space">
			<span class="submission-img-title">
				<a v-bind:href="submission.content.url"
				   target="_blank"
				   rel="nofollow"
				   class="submisison-small-thumbnail"
				   v-if="submission.content.thumbnail && !full">
					<div v-bind:style="thumbnail"
					     v-if="submission.content.thumbnail && showSmallThumbnail"
					     class="small-thumbnail"
					     @click="embedOrOpen"
					     :class="showEmbed ? 'pointer' : ''">
					</div>
				</a>

				<h1 class="submission-title"
				    v-if="full">
					<a v-bind:href="submission.content.url"
					   target="_blank"
					   rel="nofollow">
						<el-tooltip content="NSFW"
						            placement="bottom"
						            transition="false"
						            :open-delay="500">
							<i class="v-icon v-shocked go-red"
							   aria-hidden="true"
							   v-if="submission.nsfw"></i>
						</el-tooltip>

						{{ submission.title }}

						<small class="go-gray">
							- {{ submission.content.domain }}
						</small>
					</a>
				</h1>

				<span v-else
				      class="full-width">
					<h3 class="v-ultra-bold no-margin">
						<a v-bind:href="submission.content.url"
						   target="_blank"
						   rel="nofollow">
							{{ submission.title }}

							<small class="go-gray">
								- {{ submission.content.domain }}
							</small>
						</a>
					</h3>

					<submission-footer :url="url"
					                   :comments="comments"
					                   :bookmarked="bookmarked"
					                   :submission="submission"
					                   @bookmark="$emit('bookmark')"
					                   @report="$emit('report')"
					                   @hide="$emit('hide')"
					                   @nsfw="$emit('nsfw')"
					                   @sfw="$emit('sfw')"
					                   @destroy="$emit('destroy')"
					                   @approve="$emit('approve')"
					                   @disapprove="$emit('disapprove')"
					                   @removethumbnail="$emit('removethumbnail')"
					                   :upvoted="upvoted"
					                   :downvoted="downvoted"
					                   :points="points"
					                   @upvote="$emit('upvote')"
					                   @downvote="$emit('downvote')"></submission-footer>
				</span>
			</span>
		</div>
	</div>
</template>

<script>
import EmbedValidator from '../../mixins/EmbedValidator';
import SubmissionFooter from '../../components/SubmissionFooter.vue';

export default {
    mixins: [EmbedValidator],

    components: {
        SubmissionFooter
    },

    props: [
        'nsfw',
        'submission',
        'full',
        'url',
        'comments',
        'bookmarked',
        'points',
        'downvoted',
        'upvoted'
    ],

    data() {
        return {
            auth
        };
    },

    computed: {
        thumbnail() {
            return {
                backgroundImage:
                    'url(' + this.submission.content.thumbnail + ')'
            };
        },

        showBigThumbnail() {
            if (this.full) return true;

            if (this.nsfw) return false;

            return false;
        },

        showSmallThumbnail() {
            return !this.showBigThumbnail && !this.nsfw;
        },

        showEmbed() {
            return this.isValidSourceForEmbed && this.submission.content.embed;
        },

        isVideo() {
            return this.submission.content.type == 'video';
        }
    },

    methods: {
        /**
         * It emits the event to open the EmbedViewer if is allowed to. And other
         * wise it opens the url in a new tab.
         *
         * @return void
         */
        embedOrOpen(event) {
            if (this.showEmbed) {
                // prevent the browser from opening the URL
                event.preventDefault();

                // Emit the embed event
                this.$emit('embed');
            }
        }
    }
};
</script>
