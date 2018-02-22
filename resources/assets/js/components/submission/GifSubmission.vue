<style lang="scss">
.gif-submission {
    max-height: 500px;

    img {
        max-height: 500px;
    }

    video {
        max-height: 500px;
    }
}
</style>


<template>
	<div>
		<div v-if="showBigThumbnail"
		     class="align-center gif-submission">
			<video loop
			       controls
			       autoplay
			       :poster="submission.content.thumbnail_path"
                   onclick="this.paused ? this.play() : this.pause();">
				<source :src="submission.content.mp4_path"
				        type="video/mp4">
			</video>
		</div>

		<div class="link-list-info">
			<span class="submission-img-title">
				<a class="submisison-small-thumbnail"
				   v-if="submission.content.thumbnail_path && !full">
					<!-- img -->
					<div :style="thumbnail"
					     v-if="showSmallThumbnail"
					     class="small-thumbnail zoom-in"
					     @click="$emit('play-gif')"></div>
				</a>

				<h1 class="submission-title"
				    v-if="full">
                    <el-tooltip content="NSFW" placement="bottom" transition="false" :open-delay="500">
                        <i class="v-icon v-shocked go-red" aria-hidden="true" v-if="submission.nsfw"></i>
                    </el-tooltip>

					{{ submission.title }}
				</h1>

				<div class="flex1"
				     v-else>
					<router-link :to="'/c/' + submission.channel_name + '/' + submission.slug"
					             class="flex-space v-ultra-bold">
						{{ submission.title }}
					</router-link>

					<submission-footer :url="url"
					                   :comments="comments"
					                   :bookmarked="bookmarked"
					                   :submission="submission"
					                   v-if="!full"
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
				</div>
			</span>
		</div>
	</div>
</template>

<script>
import SubmissionFooter from '../../components/SubmissionFooter.vue';

export default {
    components: { SubmissionFooter },

    data() {
        return {
            auth
        };
    },

    props: {
        nsfw: {},
        submission: {},
        bookmarked: {},
        url: {},
        comments: {},
        upvoted: {},
        downvoted: {},
        points: {},
        full: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        thumbnail() {
            return {
                backgroundImage:
                    'url(' + this.submission.content.thumbnail_path + ')'
            };
        },

        showBigThumbnail() {
            if (this.full) return true;

            if (this.nsfw) return false;

            return false;
        },

        showSmallThumbnail() {
            return !this.showBigThumbnail && !this.nsfw;
        }
    }
};
</script>
