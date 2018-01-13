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
			<img :src="submission.data.thumbnail_path"
			     :alt="submission.title"
			     @click="$emit('zoom', submission.data)"
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

			<!-- <img :src="value.thumbnail_path"
			     v-for="(value, index) in photos"
			     :key="value.id"
			     @click="$emit('zoom', index)"
			     :alt="submission.title"
			     class="margin-bottom-1" /> -->
		</div>

		<div class="link-list-info">
			<span class="submission-img-title">
				<a class="submisison-small-thumbnail"
				   v-if="submission.data.thumbnail_path && !full">
					<!-- img -->
					<div v-if="showSmallThumbnail"
					     class="small-thumbnail zoom-in"
					     :style="thumbnail"
					     @click="$emit('zoom', submission.data)"></div>
				</a>

				<div class="flex1">
					<router-link :to="'/c/' + submission.channel_name + '/' + submission.slug"
					             class="flex-space v-ultra-bold"
					             v-if="!full">
						{{ submission.title }}
					</router-link>

					<h3 class="submission-title"
					    v-else>
						<el-tooltip content="NSFW"
						            placement="bottom"
						            transition="false"
						            :open-delay="500">
							<i class="v-icon v-shocked go-red"
							   aria-hidden="true"
							   v-if="submission.nsfw"></i>
						</el-tooltip>

						{{ submission.title }}
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
					                   :upvoted="upvoted"
					                   :downvoted="downvoted"
					                   :points="points"
					                   @upvote="$emit('upvote')"
					                   @downvote="$emit('downvote')"></submission-footer>
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
        SubmissionFooter, 
    },

    mixins: [Helpers],

    props: {
        nsfw: {},
        submission: {},
        bookmarked: {},
        url: {},
        comments: {},
        upvoted: {},
        downvoted: {},
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
                backgroundImage: 'url(' + this.submission.data.thumbnail_path + ')'
            };
        },

        isAlbum() {
            return this.photos.length > 1;
        },

        showBigThumbnail() {
            if (this.full) return true;

            if (this.nsfw) return false;

            return !auth.submission_small_thumbnail;
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

        getPhotos: function() {
            axios
                .get('/submission-photos', {
                    params: {
                        id: this.submission.id
                    }
                })
                .then(response => {
                    this.photos = response.data;
                });
        }
    }
};
</script>
