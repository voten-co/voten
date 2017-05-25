<template>
	<div class="link-list-info">
		<div v-if="full">
			<h1 class="submission-title">
				<i class="v-icon v-shocked go-red" aria-hidden="true" v-if="submission.nsfw"
					data-toggle="tooltip" data-placement="bottom" title="NSFW"
				></i>

				{{ submission.title }}
			</h1>

			<markdown :text="submission.data.text" v-if="submission.data.text"></markdown>
		</div>

		<div v-else>
			<router-link :to="'/c/' + submission.category_name + '/' + submission.slug"
			class="flex-space v-ultra-bold">
				{{ submission.title }}
			</router-link>

			<submission-footer :url="url" :comments="comments" :bookmarked="bookmarked" :submission="submission"
			@bookmark="$emit('bookmark')" @report="$emit('report')" @hide="$emit('hide')" @nsfw="$emit('nsfw')" @sfw="$emit('sfw')" @destroy="$emit('destroy')" @approve="$emit('approve')" @disapprove="$emit('disapprove')" @removethumbnail="$emit('removethumbnail')" :upvoted="upvoted" :downvoted="downvoted" :points="points"
			@upvote="$emit('upvote')" @downvote="$emit('downvote')"
			></submission-footer>
		</div>
	</div>
</template>

<script>
    import Markdown from '../../components/Markdown.vue';
	import SubmissionFooter from '../../components/SubmissionFooter.vue';

    export default {
        props: {
        	nsfw: {},
        	submission: {},
        	url: {},
        	comments: {},
        	bookmarked: {},
        	upvoted: {},
        	downvoted: {},
        	points: {},
            full: {
                type: Boolean,
                default: false,
            },
        },

        components: {
            Markdown,
            SubmissionFooter
        },

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip()
			})
		},

    }
</script>
