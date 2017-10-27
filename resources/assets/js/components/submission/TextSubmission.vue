<template>
	<div class="link-list-info">
		<!-- submission page -->
		<div v-if="full">
			<h1 class="submission-title">
				<i class="v-icon v-shocked go-red" aria-hidden="true" v-if="submission.nsfw" v-tooltip.bottom="{content: 'NSFW'}"></i>

				{{ submission.title }}
			</h1>

			<markdown :text="body" v-if="body && !editing"></markdown>

			<textarea class="form-control v-input-big" rows="3" id="text" placeholder="Text(optional)..." v-show="editing"
                    v-model="editedBody"
            ></textarea>

			<div class="flex-space margin-top-1" v-show="editing">
				<div>
					<button type="submit" class="v-button v-button--green" @click="patch">
						Edit
					</button>
					<button type="submit" class="v-button v-button--link" @click="cancelEditing">
						Cancel
					</button>
				</div>

				<div>
					<button type="button" class="v-button v-button--link" @click="$eventHub.$emit('markdown-guide')">
						Formatting Guide
					</button>

					<button class="v-button v-button--link" @click="preview = !preview" type="button">
						Preview
					</button>
				</div>
			</div>

			<div v-if="editing && preview" class="form-wrapper margin-bottom-1 margin-top-1 preview">
				<markdown v-if="editedBody" :text="editedBody"></markdown>
			</div>
		</div>

		<!-- submission indexing pages -->
		<div v-else>
			<h3 class="title">
				<router-link :to="'/c/' + submission.category_name + '/' + submission.slug"
					class="flex-space v-ultra-bold"
				>
					{{ submission.title }}
				</router-link>
			</h3>

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
        data() {
            return {
                editing: false,
                body: this.submission.data.text,
				editedBody: this.submission.data.text, 
				preview: false, 
            }
        },

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

        watch: {
            'submission' () {
                this.body = this.submission.data.text;
				this.editedBody = this.submission.data.text;
            }
        },

        created() {
        	this.$eventHub.$on('edit-submission', this.editSubmission);
        },

		mounted () {
			this.$nextTick(function () {
	        	this.$root.autoResize();
			})
		},

		methods: {
			/**
			 * opens the edit form
			 *
			 * @return void
			 */
			editSubmission() {
			    this.editing = !this.editing;
			},

			/**
			 * patches the TextSubmission
			 *
			 * @return void
			 */
			patch() {
				axios.post('/patch-text-submission', {
					id: this.submission.id,
					text: this.editedBody
				})
				.then((response) => {
					this.body = this.editedBody;
					this.editing = false;
				}).catch((error) => {
					this.editing = true;
				});
			},

			/**
			 * cancels editing the TextSubmission
			 *
			 * @return void
			 */
			cancelEditing() {
				this.editedBody = this.body;
			    this.editing = false;
			},
		}
    }
</script>
