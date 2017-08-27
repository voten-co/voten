<template>
	<div class="index-submission-footer user-select">
		<div :class="auth.isMobileDevice ? 'flex-space' : 'display-inline'">
			<div :class="auth.isMobileDevice ? '' : 'display-inline'">
				<router-link :to="url" class="comments-icon h-green"
				data-toggle="tooltip" data-placement="top" title="Comments">
					<i class="v-icon v-chat"></i><span v-if="comments" v-text="comments"></span>
				</router-link>

				<a @click="$emit('bookmark')"
					data-toggle="tooltip" data-placement="top" title="Bookmark">
					<i class="v-icon h-yellow pointer" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
				</a>

				<div class="ui icon top left pointing dropdown" v-if="!isGuest" id="more-button">
					<i class="v-icon v-more" aria-hidden="true"></i>

					<div class="menu">
						<button class="item" @click="$emit('report')" v-if="!owns">
							Report
						</button>

						<button class="item" @click="$emit('hide')" v-if="!owns">
							Hide
						</button>

						<button class="item" @click="$emit('nsfw')" v-if="showNSFW">
							NSFW
						</button>

						<button class="item" @click="$emit('sfw')" v-if="showSFW">
							Family Safe
						</button>

						<button class="item" @click="$emit('destroy')" v-if="owns">
							Delete
						</button>

						<button class="item" @click="$emit('approve')" v-if="showApprove">
							Approve
						</button>

						<button class="item" @click="$emit('disapprove')" v-if="showDisapprove">
							Delete
						</button>

						<button class="item" @click="$emit('removethumbnail')" v-if="showRemoveTumbnail">
							Remove Thumbnail
						</button>
					</div>
				</div>
			</div>

			<div class="voting-wrapper display-none mobile-only">
				<a class="fa-stack align-right" @click="$emit('upvote')"
					data-toggle="tooltip" data-placement="top" title="Upvote">
					<i class="v-icon v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
				</a>

				<div class="detail">
					{{ points }} Points
				</div>

				<a class="fa-stack align-right" @click="$emit('downvote')"
					data-toggle="tooltip" data-placement="top" title="Downvote">
					<i class="v-icon v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
				</a>
			</div>
		</div>

		<span class="desktop-only">
			Submitted {{ date }} by
			<router-link :to="'/' + '@' + submission.owner.username" class="h-underline desktop-only">
				{{ '@' + submission.owner.username }}
			</router-link>
			to <router-link :to="'/c/' + submission.category_name" class="category-label h-underline">#{{ submission.category_name }}</router-link>
		</span>

		<div class="mobile-only mobile-submission-item-action">
			{{ date }} by
			<router-link :to="'/' + '@' + submission.owner.username" class="h-underline">
				{{ '@' + submission.owner.username }}
			</router-link>
			to <router-link :to="'/c/' + submission.category_name" class="category-label h-underline">#{{ submission.category_name }}</router-link>
		</div>
	</div>
</template>

<script>
	import Helpers from '../mixins/Helpers';

    export default {
    	mixins: [Helpers],

        props: [
        	'url', 'comments', 'bookmarked', 'submission', 'upvoted', 'downvoted', 'points'
        ],

        data () {
            return {
                auth,
                Store
            }
        },

        computed: {
        	/**
        	 * Does the auth user own the submission
        	 *
        	 * @return Boolean
        	 */
        	owns() {
        		return auth.id == this.submission.owner.id
        	},

        	showApprove(){
				return !this.submission.approved_at && Store.moderatingAt.indexOf(this.submission.category_id) != -1 && !this.owns
			},

			showDisapprove(){
				return !this.submission.deleted_at && Store.moderatingAt.indexOf(this.submission.category_id) != -1 && !this.owns
			},

			showNSFW(){
				return (this.owns || Store.moderatingAt.indexOf(this.submission.category_id) != -1) && !this.submission.nsfw
			},

			showSFW(){
				return (this.owns || Store.moderatingAt.indexOf(this.submission.category_id) != -1) && this.submission.nsfw
			},

			showRemoveTumbnail(){
				if (this.owns && this.submission.data.thumbnail)
					return true
				return false
			},

            date () {
                return moment(this.submission.created_at).utc(moment().format("Z")).fromNow()
            },
        },

        mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip()
	        	this.$root.loadSemanticDropdown()
			})
        }
    };
</script>
