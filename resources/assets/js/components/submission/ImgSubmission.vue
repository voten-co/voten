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
				<a class="submisison-small-thumbnail" v-if="submission.data.thumbnail_path && !full">
					<!-- img -->
					<div v-if="showSmallThumbnail" class="small-thumbnail zoom-in" v-bind:style="thumbnail"
					@click="$emit('zoom')"></div>
				</a>

				<div class="flex1">
					<router-link :to="'/c/' + submission.category_name + '/' + submission.slug" class="flex-space v-ultra-bold">
						{{ submission.title }}
					</router-link>

					<submission-footer :url="url" :comments="comments" :bookmarked="bookmarked" :submission="submission" v-if="!full"
					@bookmark="$emit('bookmark')" @report="$emit('report')" @hide="$emit('hide')" @nsfw="$emit('nsfw')" @sfw="$emit('sfw')" @destroy="$emit('destroy')" @approve="$emit('approve')" @disapprove="$emit('disapprove')" @removethumbnail="$emit('removethumbnail')" :upvoted="upvoted" :downvoted="downvoted" :points="points"
					@upvote="$emit('upvote')" @downvote="$emit('downvote')"
					></submission-footer>
				</div>
			</span>
		</div>
	</div>
</template>

<script>
	import SubmissionFooter from '../../components/SubmissionFooter.vue';

	export default {
		components: {
			SubmissionFooter
		},

	    props: {
	    	nsfw: {}, submission: {}, bookmarked:{}, url: {}, comments: {}, upvoted: {}, downvoted: {}, points: {},
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
			thumbnail() {
				return {
					backgroundImage: 'url(' + this.submission.data.thumbnail_path + ')'
				};
			},

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
	    		axios.get('/submission-photos', {
	    			params: {
	    				id: this.submission.id
	    			}
	    		}).then((response) => {
	                this.photos = response.data
	            });
	    	},
	    }
	}
</script>
