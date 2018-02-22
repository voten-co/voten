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
			<video loop
			       controls
			       autoplay
			       onclick="this.paused ? this.play() : this.pause();"
			       :poster="submission.content.thumbnail_path">
				<source :src="submission.content.mp4_path"
				        type="video/mp4">
			</video>
		</div>
	</el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    props: ['visible'],

    mixins: [Helpers],

    beforeDestroy() {
        if (window.location.hash == '#gifPlayer') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'gifPlayer';
    },

    computed: {
        submission() {
            return Store.modals.gifPlayer.submission;
        },

        gif() {
            return Store.modals.gifPlayer.gif;
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
