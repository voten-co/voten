<style lang="scss">
.photo-viewer {
    iframe {
        width: 70em;
        max-width: 100%;
        height: 36em;
        max-height: 48%;
    }
}
</style>

<template>
	<el-dialog :title="submission.title"
	           :visible="visible"
	           @close="close"
	           append-to-body
	           fullscreen
	           class="user-select">
               <div slot="title">
                   <span class="v-bold margin-right-1">
                       {{ submission.title }} 
                   </span>

                   <el-button round type="success"
                              size="mini"
                              plain
                              @click="goToSubmission"
                              icon="margin-right-half v-comment"
                              v-if="$route.name != 'submission-page'">{{ submission.comments_count > 0 ? submission.comments_count + ' Comments' : 'Comment' }}</el-button>
               </div>

		<div class="photo-viewer">
			<div v-html="submission.content.embed"
			     class="video-player-wrapper"></div>
		</div>
	</el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    mixins: [Helpers],

    props: ['visible'],

    beforeDestroy() {
        if (window.location.hash == '#embedViewer') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'embedViewer';
    },

    computed: {
        submission() {
            return Store.modals.embedViewer.submission;
        }
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        },

        goToSubmission() {
            this.$router.push(
                '/c/' +
                    this.submission.channel_name +
                    '/' +
                    this.submission.slug
            );
            this.close();
        }
    }
};
</script>
