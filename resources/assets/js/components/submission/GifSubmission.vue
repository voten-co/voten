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
		<div class="align-center gif-submission">
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
				<h1 class="submission-title">
					{{ submission.title }}

					<el-tag size="mini"
					        type="danger"
					        class="margin-left-half"
					        v-if="submission.nsfw">NSFW</el-tag>
				</h1>
			</span>
		</div>
	</div>
</template>

<script>
export default {
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
        likes: {},
        points: {}
    },

    computed: {
        thumbnail() {
            return {
                backgroundImage: 'url(' + this.submission.content.thumbnail_path + ')'
            };
        }
    }
};
</script>
