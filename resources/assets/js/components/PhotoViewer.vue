<template>
	<el-dialog :title="submission.title"
	           :visible="visible"
	           @close="close"
	           append-to-body
	           fullscreen
	           class="user-select">
		<div slot="title">
			<a :download="imageToDisplay"
			   :href="imageToDisplay"
			   :title="submission.title"
			   class="margin-right-half">
				<el-button plain
				           type="info"
				           size="mini"
				           icon="el-icon-download">
					Download photo
				</el-button>
			</a>

			<el-button type="primary"
			           size="mini"
			           plain
			           @click="openAlbum"
			           icon="el-icon-picture"
			           v-if="isAlbum && $route.name != 'submission-page'">See full album</el-button>
		</div>

		<div class="photo-viewer">
			<img :src="imageToDisplay"
			     :alt="submission.title"
			     @click="close">
		</div>
	</el-dialog>
</template>

<script>
import Helpers from '../mixins/Helpers';

export default {
    props: ['visible'],

    mixins: [Helpers],

    beforeDestroy() {
        if (window.location.hash == '#photoViewer') {
            history.go(-1);
        }
    },

    created() {
        window.location.hash = 'photoViewer';
    },

    computed: {
        isAlbum() {
            return this.submission.data.album;
        },

        imageToDisplay() {
            if (this.image) {
                return this.image.path;
            }

            return this.submission.data.path;
        },

        image() {
            return Store.modals.photoViewer.image;
        },

        submission() {
            return Store.modals.photoViewer.submission;
        }
    },

    methods: {
        close() {
            this.$emit('update:visible', false);
        },

        openAlbum() {
            this.$router.push('/c/' + this.submission.channel_name + '/' + this.submission.slug);
            this.close();
        }
    }
};
</script>
