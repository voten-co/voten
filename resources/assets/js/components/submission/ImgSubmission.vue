<template>
	<div>
		<div v-if="!isAlbum && showBigThumbnail">
			<img v-bind:src="submission.data.thumbnail_path" v-bind:alt="submission.title" @click="$emit('zoom')" class="big-thumbnail"/>
		</div>

		<div v-if="isAlbum && showBigThumbnail">
			<img v-bind:src="value.thumbnail_path" v-for="(value, index) in photos"
			@click="$emit('zoom', index)" v-bind:alt="submission.title" class="margin-bottom-1" />
		</div>

		<div class="link-list-info">
			<span class="submission-img-title">
				<img v-bind:src="submission.data.thumbnail_path" v-bind:alt="submission.title"
				v-if="showSmallThumbnail" class="small-thumbnail" @click="$emit('zoom')"/>

				<router-link :to="'/c/' + submission.category_name + '/' + submission.slug" class="flex-space v-ultra-bold">
					{{ submission.title }}
				</router-link>
			</span>
		</div>
	</div>
</template>

<script>
export default {
    props: {
    	nsfw: {}, submission: {},
        full: {
            type: Boolean,
            default: false,
        },
    },

    data: function () {
        return {
			auth,
            photos: [],
        };
    },

	computed: {
		isAlbum(){
			return this.photos.length > 1
		},

		showBigThumbnail(){
			if (this.full) return true

			if (this.nsfw) return false

			return ! auth.submission_small_thumbnail
		},

		showSmallThumbnail(){
			return ! this.showBigThumbnail && !this.nsfw
		}
	},

    created: function() {
        if(this.full){
        	this.getPhotos()
        }
    },

    methods: {
    	getPhotos: function () {
    		axios.post('/submission-photos', { id: this.submission.id } ).then((response) => {
                this.photos = response.data
            });
    	},
    }
}
</script>
