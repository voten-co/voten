<template>
	<el-dialog :title="submission.title"
	           :visible="visible"
	           @close="close"
	           append-to-body
	           fullscreen
	           class="user-select">
		<div class="photo-viewer">
			<video loop
			       controls
			       autoplay
			       onclick="this.paused ? this.play() : this.pause();"
			       :poster="submission.data.thumbnail_path">
				<source :src="submission.data.mp4_path"
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

    computed: {
        submission() {
            return Store.gifPlayer.submission; 
        }, 

        gif() {
            return Store.gifPlayer.gif; 
        }
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        }
    }
};
</script>
