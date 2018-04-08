<template>
	<div class="link-list-info">
		<!-- submission page -->
		<div v-if="full">
			<h1 class="submission-title">
				{{ submission.title }}

                <el-tag size="mini" type="danger" class="margin-left-half" v-if="submission.nsfw">NSFW</el-tag>
			</h1>

			<markdown :text="body" v-if="body && !editing"></markdown>

			<el-input
					v-show="editing"
					type="textarea"
					:autosize="{ minRows: 4, maxRows: 25 }"
					id="text"
					placeholder="Text(optional)..."
					v-model="editedBody"
					:maxlength="15000"
			></el-input>

			<div class="flex-space margin-top-1" v-show="editing">
				<div>
					<el-button round type="success" @click="patch" :loading="loading" size=mini>
						Save
					</el-button>
					<el-button type="text" @click="cancelEditing" size="mini">
						Cancel
					</el-button>
				</div>

				<div>
					<el-button type="text" @click="$eventHub.$emit('markdown-guide')" size="mini">
						Formatting Guide
					</el-button>

					<el-button @click="preview = !preview" type="text" size="mini" 
						:icon="preview ? 'el-icon-close' : 'el-icon-view'"
					>
						Preview
					</el-button>
				</div>
			</div>

			<div v-if="editing && preview" class="form-wrapper margin-bottom-1 margin-top-1 preview">
				<markdown v-if="editedBody" :text="editedBody.trim()"></markdown>
			</div>
		</div>

		<!-- submission indexing pages -->
		<div v-else>
			<h3 class="title">
				<router-link :to="'/c/' + submission.channel_name + '/' + submission.slug"
					class="flex-space v-ultra-bold"
				>
					{{ submission.title }}

                    <el-tag size="mini" type="danger" class="margin-left-half" v-if="submission.nsfw">NSFW</el-tag>
				</router-link>
			</h3>

			<submission-footer :url="url" :comments="comments" :bookmarked="bookmarked" :submission="submission"
				@bookmark="$emit('bookmark')" @report="$emit('report')" @hide="$emit('hide')" @nsfw="$emit('nsfw')" @sfw="$emit('sfw')" @destroy="$emit('destroy')" @approve="$emit('approve')" @disapprove="$emit('disapprove')" @removethumbnail="$emit('removethumbnail')" :liked="liked" :points="points"
				@like="$emit('like')"
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
            body: this.submission.content.text,
            editedBody: this.submission.content.text,
            preview: false,
            loading: false
        };
    },

    props: {
        nsfw: {},
        submission: {},
        url: {},
        comments: {},
        bookmarked: {},
        liked: {},
        points: {},
        full: {
            type: Boolean,
            default: false
        }
    },

    components: {
        Markdown,
        SubmissionFooter
    },

    watch: {
        submission() {
            this.body = this.submission.content.text;
            this.editedBody = this.submission.content.text;
        }
    },

    created() {
        this.$eventHub.$on('edit-submission', this.editSubmission);
    },

    beforeDestroy() {
        this.$eventHub.$off('edit-submission', this.editSubmission);
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
            this.loading = true;

            axios
                .patch(`/submissions/${this.submission.id}`, {
                    text: this.editedBody
                })
                .then(() => {
                    this.body = this.editedBody;
                    this.editing = false;
                    this.loading = false;
                })
                .catch(() => {
                    this.editing = true;
                    this.loading = false;
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
        }
    }
};
</script>
