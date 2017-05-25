<template>
    <div>
        <div v-if="showBigThumbnail" class="align-center relative pointer" @click="$emit('play-gif')">
            <img v-bind:src="submission.data.thumbnail_path" v-bind:alt="submission.title" class="big-thumbnail" />

            <span class="play-gif">
                <i class="v-icon v-gif-1"></i>
            </span>
        </div>

        <div class="link-list-info">
            <span class="submission-img-title">
	            <a class="submisison-small-thumbnail" v-if="submission.data.thumbnail_path && !full">
	            	<!-- img -->
					<div v-bind:style="thumbnail"
	                v-if="showSmallThumbnail" class="small-thumbnail zoom-in" @click="$emit('play-gif')"></div>
	            </a>

				<h1 class="submission-title" v-if="full">
					<router-link :to="'/c/' + submission.category_name + '/' + submission.slug" class="flex-space v-ultra-bold">
	                    {{ submission.title }}
	                </router-link>
				</h1>

                <div class="flex1" v-else>
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

        mixins: [],

        data () {
            return {
                auth
            }
        },

        props: {
        	nsfw: {}, submission: {}, bookmarked:{}, url: {}, comments: {}, upvoted: {}, downvoted: {}, points: {},
            full: {
                type: Boolean,
                default: false,
            },
        },

        computed: {
        	thumbnail() {
				return {
					backgroundImage: 'url(' + this.submission.data.thumbnail_path + ')'
				};
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

        created () {
            //
        },

        mounted () {
            //
        },

        methods: {
            //
        }
    };
</script>
