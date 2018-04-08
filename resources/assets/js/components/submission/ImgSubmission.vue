<style lang="scss">
.img .profile-post-content img {
    cursor: zoom-in;
    height: 100%;
    width: auto;
}
</style>


<template>
	<div>
		<div v-if="!isAlbum && showBigThumbnail">
			<img :src="submission.content.thumbnail_path"
			     :alt="submission.title"
			     @click="$emit('zoom', submission.content)"
			     class="big-thumbnail" />
		</div>

		<div v-if="isAlbum && showBigThumbnail">
			<el-carousel :height="isMobile ? '200px' : '500px'"
			             :autoplay="false"
			             arrow="always">
				<el-carousel-item v-for="item in photos"
				                  :key="item.id">
					<img :src="item.thumbnail_path"
					     @click="$emit('zoom', item)"
					     :alt="submission.title">
				</el-carousel-item>
			</el-carousel>
		</div>

		<div class="link-list-info">
			<span class="submission-img-title">
				<a class="submisison-small-thumbnail"
				   v-if="submission.content.thumbnail_path && !full">
					<!-- img -->
					<div v-if="showSmallThumbnail"
					     class="small-thumbnail zoom-in"
					     :style="thumbnail"
					     @click="$emit('zoom', submission.content)"></div>
				</a>

				<div class="flex1">
					<router-link :to="'/c/' + submission.channel_name + '/' + submission.slug"
					             class="flex-space v-ultra-bold"
					             v-if="!full">
						{{ submission.title }}

						<el-tag size="mini" type="danger" class="margin-left-half" v-if="submission.nsfw">NSFW</el-tag>
					</router-link>

					<h3 class="submission-title" v-else>
						{{ submission.title }}

						<el-tag size="mini" type="danger" class="margin-left-half" v-if="submission.nsfw">NSFW</el-tag>
					</h3>

					<submission-footer :url="url"
					                   :comments="comments"
					                   :bookmarked="bookmarked"
					                   :submission="submission"
					                   v-if="!full"
					                   @bookmark="$emit('bookmark')"
					                   @report="$emit('report')"
					                   @hide="$emit('hide')"
					                   @nsfw="$emit('nsfw')"
					                   @sfw="$emit('sfw')"
					                   @destroy="$emit('destroy')"
					                   @approve="$emit('approve')"
					                   @disapprove="$emit('disapprove')"
					                   @removethumbnail="$emit('removethumbnail')"
					                   :liked="liked"
					                   :points="points"
					                   @like="$emit('like')"></submission-footer>
				</div>
			</span>
		</div>
	</div>
</template>

<script>
import SubmissionFooter from '../../components/SubmissionFooter.vue';
import Helpers from '../../mixins/Helpers';

export default {
    components: {
        SubmissionFooter
    },

    mixins: [Helpers],

    props: {
        nsfw: {},
        submission: {},
        bookmarked: {},
        url: {},
        comments: {},
        liked: {},
        points: {},
        full: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            photos: [],
            currentImageIndex: 0
        };
    },

    computed: {
        thumbnail() {
            return {
                backgroundImage:
                    'url(' + this.submission.content.thumbnail_path + ')'
            };
        },

        isAlbum() {
            return this.photos.length > 1;
        },

        showBigThumbnail() {
            if (this.full) return true;

            if (this.nsfw) return false;

            return false;
        },

        showSmallThumbnail() {
            return !this.showBigThumbnail && !this.nsfw;
        }
    },

    created: function() {
        if (this.full) {
            this.getPhotos();
        }
    },

    methods: {
        setCurrentImage(index) {
            this.currentImageIndex = index;
        },

        getPhotos() {
            axios
                .get('/submissions/photos', {
                    params: {
                        submission_id: this.submission.id
                    }
                })
                .then((response) => {
                    this.photos = response.data.data;
                });
        }
    }
};
</script>
