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
		<div v-if="submission.content.thumbnail" class="submission-page-preview">
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
				<h1 class="submission-title">
					<a v-bind:href="submission.content.url"
					   target="_blank"
					   rel="nofollow">
						{{ submission.title }}

						<small class="go-gray">
							- {{ submission.content.domain }}
						</small>

						<el-tag size="mini" type="danger" class="margin-left-half" v-if="submission.nsfw">NSFW</el-tag>
					</a>
				</h1>
			</span>
		</div>
	</div>
</template>

<script>
import EmbedValidator from '../../mixins/EmbedValidator';

export default {
	mixins: [EmbedValidator],
	
    props: [
        'nsfw',
        'submission',
        'url',
        'comments',
        'bookmarked',
        'points',
        'liked'
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
		
        showEmbed() {
            return this.isValidSourceForEmbed && this.submission.content.embed;
        },

        isVideo() {
            return this.submission.content.type == 'video';
        }
    }
};
</script>
