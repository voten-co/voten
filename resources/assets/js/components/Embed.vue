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
		<!-- <div slot="title">
			<a :href="submission.data.url"
			   target="_blank">
				<el-button type="info"
				           plain
				           size="mini">
					Open link in a new tab
				</el-button>
			</a>
		</div> -->

		<div class="photo-viewer">
			<div v-html="submission.data.embed"
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
        }
    }
};
</script>
