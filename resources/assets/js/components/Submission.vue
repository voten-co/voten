<template>
	<transition name="fade">
		<div class="submission-wrapper" v-show="!hidden">
			<!-- side-voting -->
			<div class="side-voting desktop-only" v-if="!full">
				<a class="fa-stack align-right" @click="voteUp"
					data-toggle="tooltip" data-placement="top" title="Upvote">
					<i class="v-icon v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
				</a>

				<div class="user-select vote-number">
					{{ points }}
				</div>

				<a class="fa-stack align-right" @click="voteDown"
					data-toggle="tooltip" data-placement="bottom" title="Downvote">
					<i class="v-icon v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
				</a>
			</div>

			<article class="flex1" v-bind:class="'box-typical profile-post ' + list.type">


				<!-- header -->
				<div class="profile-post-header user-select">
					<div class="user-card-row">
						<div class="tbl-row">
							<div class="tbl-cell tbl-cell-photo">
								<router-link :to="'/' + '@' + list.owner.username">
									<img v-bind:src="list.owner.avatar"  v-bind:alt="list.owner.username">
								</router-link>
							</div>

							<div class="tbl-cell">
								<div class="user-card-row-name">
									<router-link :to="'/' + '@' + list.owner.username">
										@{{ list.owner.username }}
									</router-link>
								</div>
								<div class="color-blue-grey-lighter">
									<router-link :to="'/c/' + list.category_name + '/' + list.slug">
										{{ date }}
									</router-link>

									- Submitted to

									<router-link :to="'/c/' + list.category_name" class="category-label">
										#{{ list.category_name }}
									</router-link>
								</div>
							</div>
						</div>
					</div>

					<div class="card-user-action shared">
						<i class="v-icon v-shocked go-red" aria-hidden="true" v-if="list.nsfw"
							data-toggle="tooltip" data-placement="bottom" title="NSFW"
						></i>

						<router-link :to="'/c/' + list.category_name + '/' + list.slug" class="go-gray">
							<i class="v-icon" aria-hidden="true" :class="typeIcon"
							data-toggle="tooltip" data-placement="bottom" :title="typeTooltip"></i>
						</router-link>
					</div>
				</div>


				<!-- content -->
				<div class="profile-post-content">
					<text-submission v-if="list.type == 'text'" :submission="list" :nsfw="nsfw" :full="full"></text-submission>

					<img-submission v-if="list.type == 'img'" :submission="list" :nsfw="nsfw" :full="full"
						@zoom="showPhotoViewer"
					></img-submission>

					<gif-submission v-if="list.type == 'gif'" :submission="list" :nsfw="nsfw" :full="full"
						@play-gif="showGifPlayer"
					></gif-submission>

					<link-submission v-if="list.type == 'link'" :submission="list" :nsfw="nsfw" :full="full"
						@embed="showEmbed"
					></link-submission>
				</div>


				<!-- footer -->
				<div class="box-typical-footer profile-post-meta user-select">
					<div class="voting-wrapper" :class="!full ? 'mobile-only' : 'display-none'">
						<a class="fa-stack align-right" @click="voteUp"
							data-toggle="tooltip" data-placement="top" title="Upvote">
							<i class="v-icon v-up-fat" :class="upvoted ? 'go-primary' : 'go-gray'"></i>
						</a>

						<div class="detail">
							{{ points }} Points
						</div>

						<a class="fa-stack align-right" @click="voteDown"
							data-toggle="tooltip" data-placement="top" title="Downvote">
							<i class="v-icon v-down-fat" :class="downvoted ? 'go-red' : 'go-gray'"></i>
						</a>
					</div>

					<div>
						<router-link :to="'/c/' + list.category_name + '/' + list.slug" class="fa-stack h-green" v-if="!full"
						data-toggle="tooltip" data-placement="top" title="Comments">
							<i class="v-icon v-chat"></i><span v-if="list.comments_number" v-text="list.comments_number"></span>
						</router-link>

						<a class="fa-stack" @click="bookmark"
							data-toggle="tooltip" data-placement="top" title="Bookmark">
							<i class="v-icon h-yellow" :class="bookmarked ? 'go-yellow v-unbookmark' : 'v-bookmark'"></i>
						</a>

						<div class="ui icon top right pointing dropdown" data-toggle="tooltip" data-placement="top" title="More">
							<i class="v-icon v-more" aria-hidden="true"></i>

							<div class="menu">
								<button class="item" @click="report" v-if="!owns">
									<i class="v-icon v-flag-empty go-green" aria-hidden="true"></i>
									Report
								</button>

								<button class="item" @click="hide" v-if="!owns">
									<i class="v-icon v-hide go-yellow" aria-hidden="true"></i>
									Hide
								</button>

								<button class="item" @click="markAsNSFW" v-if="showNSFW">
									<i class="v-icon v-shocked" aria-hidden="true"></i>
									NSFW
								</button>

								<button class="item" @click="markAsSFW" v-if="showSFW">
									<i class="v-icon v-child" aria-hidden="true"></i>
									Family Safe
								</button>

								<button class="item" @click="destroy" v-if="owns">
									<i class="v-icon v-trash go-red" aria-hidden="true"></i>
									Delete
								</button>

								<button class="item" @click="approve" v-if="showApprove">
									<i class="v-icon v-approve go-green" aria-hidden="true"></i>
									Approve
								</button>

								<button class="item" @click="disapprove" v-if="showDisapprove">
									<i class="v-icon v-trash go-red" aria-hidden="true"></i>
									Delete
								</button>

								<button class="item" @click="removeThumbnail" v-if="showRemoveTumbnail">
									<i class="v-icon v-picture go-green" aria-hidden="true"></i>
									Remove Thumbnail
								</button>

								<!-- <button class="item" @click="newThumbnail" v-if="showNewTumbnail">
									<i class="v-icon v-picture go-green" aria-hidden="true"></i>
									New Thumbnail
								</button> -->
							</div>
						</div>
					</div>

					<div class="quick-comment relative" :class="!full ? 'desktop-only' : 'display-none'">
						<form action="/comment" method="post" @submit="submit">
							<input type="text" name="comment" placeholder="Comment..." v-model="quickComment" autocomplete="off" :disabled="sendingQuickComment">
							<button type="submit" name="button" v-show="quickComment.trim()"
							data-toggle="tooltip" data-placement="left" title="Submit">
								<i class="v-icon v-send go-green" aria-hidden="true"></i>
							</button>
						</form>
					</div>
				</div>

				<photo-viewer v-if="photoViewer" :bookmarked="bookmarked" :points="points" @close="closeViwer"
				:list="list" :photoindex="photoViewerIndex"
					:upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp" @downvote="voteDown"
				></photo-viewer>

				<embed-viewer v-if="embedViewer" :bookmarked="bookmarked" :points="points" @close="closeEmbed"
				:list="list"
					:upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp" @downvote="voteDown"
				></embed-viewer>

				<gif-player v-if="gifPlayer" :bookmarked="bookmarked" :points="points" @close="closeGifPlayer"
				:list="list"
					:upvoted="upvoted" :downvoted="downvoted" @bookmark="bookmark" @upvote="voteUp" @downvote="voteDown"
				></gif-player>
			</article>
		</div>
	</transition>
</template>

<script>
    import TextSubmission from '../components/submission/TextSubmission.vue'
    import LinkSubmission from '../components/submission/LinkSubmission.vue'
    import ImgSubmission from '../components/submission/ImgSubmission.vue'
    import GifSubmission from '../components/submission/GifSubmission.vue'
	import PhotoViewer from '../components/PhotoViewer.vue'
	import EmbedViewer from '../components/Embed.vue'
	import GifPlayer from '../components/GifPlayer.vue'

    export default {
        props: ['list', 'full'],

        components: {
            TextSubmission,
            LinkSubmission,
            ImgSubmission,
			GifSubmission,
			PhotoViewer,
			EmbedViewer,
			GifPlayer,
        },

        data () {
            return {
                bookmarked: false,
                upvoted: false,
                downvoted: false,
                hidden: false,
                reported: false,
                sendingQuickComment: false,
				quickComment: '',
				photoViewerIndex: null,
				photoViewer: false,
				embedViewer: false,
				gifPlayer: false,
                auth,
                Store
            }
        },

        created () {
        	this.setBookmarked()
        	this.setVoteds()
			this.$eventHub.$on('photo-viewer', this.showPhotoViewer)
			this.$eventHub.$on('scape', this.closeViwer)
        },

	    watch: {
			// call again the method if the route changes
			'$route' () {
	        	this.setBookmarked()
	        	this.setVoteds()
			}
		},

		mounted () {
			this.$nextTick(function () {
	        	this.$root.loadSemanticTooltip()
	        	this.$root.loadSemanticDropdown()
			})
		},


        computed: {
			points(){
				let total = this.list.upvotes - this.list.downvotes

				if (total < 0 ) return 0

				return total
			},

			typeIcon(){
				if (this.list.type == 'img') {
					return this.list.data.album ? 'v-image' : 'v-photo'
				}

				if (this.list.type == 'text') {
					return 'v-text'
				}

				if (this.list.data.type == 'video') {
					return 'v-video'
				}

				if (this.list.type == 'gif') {
					return 'v-gif'
				}

				return 'v-link'
			},

			/**
        	 * Does the auth user own the submission
        	 *
        	 * @return Boolean
        	 */
        	owns() {
        		return auth.id == this.list.owner.id
        	},

			typeTooltip(){
				if (this.list.type == 'img') {
					return this.list.data.album ? 'Album' : 'Image'
				}

				if (this.list.type == 'text') {
					return 'Text'
				}

				if (this.list.data.type == 'video') {
					return 'Video'
				}

				if (this.list.type == 'gif') {
					return 'GIF'
				}

				return 'Link'
			},

			showApprove(){
				return !this.list.approved_at && Store.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns
			},

			showDisapprove(){
				return !this.list.deleted_at && Store.moderatingAt.indexOf(this.list.category_id) != -1 && !this.owns
			},

			showNSFW(){
				return (this.owns || Store.moderatingAt.indexOf(this.list.category_id) != -1) && !this.list.nsfw
			},

			showSFW(){
				return (this.owns || Store.moderatingAt.indexOf(this.list.category_id) != -1) && this.list.nsfw
			},

			showRemoveTumbnail(){
				if (this.owns && this.list.data.thumbnail)
					return true
				return false
			},

            /**
             * Whether or not user wants to see NSFW content's image
             *
             * (Hint: The base idea is that we don't display NSFW content)
             * If the user wants to see NSFW media then return false, like it's not NSFW at all
             * Otherwise return true which means the media must not be displayed.
             * (false: the media will be displayed)
             *
             * @return boolean
             */
            nsfw() {
				return this.list.nsfw && !auth.nsfwMedia
            },

			/**
			 * The current vote type. It's being used to optimize the voing request on the server-side.
			 *
			 * @return mixed
			 */
			currentVote () {
			    if (this.upvoted) {
			    	return "upvote"
			    }

				if (this.downvoted) {
					return "downvote"
				}

				return null;
			},

            date () {
                return moment(this.list.created_at).utc(moment().format("Z")).fromNow()
            },
        },

        methods: {
			removeThumbnail(){
				this.list.data.thumbnail = null
				this.list.data.img = null

				axios.post('/remove-thumbnail', {
				    id: this.list.id
				})
			},

			/**
			 * Submits the (quick)comment
			 *
			 * @return void
			 */
			submit(event){
				event.preventDefault()

				if (!this.quickComment.trim()) return

				let temp = this.quickComment
				this.quickComment = ''

				this.sendingQuickComment = true

				axios.post('/comment', {
					parent_id: 0,
					submission_id: this.list.id,
					body: temp,
				}).then((response) => {
				    Store.commentUpVotes.push(response.data.id)

				    this.list.comments_number ++

				    this.sendingQuickComment = false
				}, (response) => {
					this.sendingQuickComment = false
				})
			},

			/**
			 * marks the submission as NSFW (not safe for work)
			 *
			 * @return void
			 */
			markAsNSFW() {
			     axios.post('/mark-submission-nsfw', {
			         id: this.list.id
			     }).then((response) => {
			         this.list.nsfw = true
			     })
			},

			/**
			 * marks the submission as NSFW (not safe for work)
			 *
			 * @return void
			 */
			markAsSFW() {
			     axios.post('/mark-submission-sfw', {
			         id: this.list.id
			     }).then((response) => {
			         this.list.nsfw = false
			     })
			},

        	/**
            * whether or not the user has voted on submission
            *
            * @return void
            */
            setVoteds () {
            	if (Store.submissionUpVotes.indexOf(this.list.id) != -1) {
            		this.upvoted = true
            		return
            	}

            	if (Store.submissionDownVotes.indexOf(this.list.id) != -1) {
            		this.downvoted = true
            		return
            	}

            	return
            },

        	/**
             *  Whether or not user has bookmarked the submission
             *
             *  @return Boolean
             */
            setBookmarked () { if ( Store.submissionBookmarks.indexOf(this.list.id) != -1 ) this.bookmarked = true },

        	/**
             *  Toggles the submission into bookmarks
             */
        	bookmark (submission) {
        		this.bookmarked = !this.bookmarked

				axios.post('/bookmark-submission', {
					id: this.list.id
				}).then((response) => {
					if (Store.submissionBookmarks.indexOf(this.list.id) != -1){
	                	var index = Store.submissionBookmarks.indexOf(this.list.id)
	                	Store.submissionBookmarks.splice(index, 1)

	                	return
	                }
					Store.submissionBookmarks.push(this.list.id)
				})
        	},

            /**
             *  hide(block) submission
             *
             *  @return void
             */
            hide () {
                this.hidden = true
                axios.post('/hide-submission', { submission_id: this.list.id })
            },

            /**
             * Deletes the submission. Only the owner is allowed to make such decision.
             *
             * @return void
             */
            destroy () {
                axios.post('/destroy-submission', { id: this.list.id })
                if (this.full) {
                    this.$router.push('/')
                } else {
                	this.hidden = true
                }
            },

			/**
             * Approves the submission. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
			approve(){
				axios.post('/approve-submission', {
				    submission_id: this.list.id
				}).then((response) => {
				    this.list.approved_at = moment().utc().format('YYYY-MM-DD HH:mm:ss')
				})
			},

			/**
             * Disapproves the submission. Only the moderators of category are allowed to do this.
             *
             * @return void
             */
			disapprove(){
				axios.post('/disapprove-submission', {
				    submission_id: this.list.id
				}).then((response) => {
					if (this.full) {
	                    this.$router.push('/')
	                } else {
	                	this.hidden = true
	                }
				})
			},

            /**
            *  Report submission
            *
            *  @return void
            */
            report () {
                this.reported = true
        		this.$eventHub.$emit('report-submission', this.list.id, this.list.category_name)
            },

            /**
             *  Upvote submission
             *
             *  @return void
             */
            voteUp () {
				let id = this.list.id

				axios.post('/upvote-submission', {
					submission_id: id,
					previous_vote: this.currentVote
				})

            	// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false
            		this.list.upvotes --

            		var index = Store.submissionUpVotes.indexOf(id);
                	Store.submissionUpVotes.splice(index, 1);

            		return
            	}

				// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false
            		this.list.downvotes --

            		var index = Store.submissionDownVotes.indexOf(id);
                	Store.submissionDownVotes.splice(index, 1);
            	}

            	// Not voted
            	this.upvoted = true
            	this.list.upvotes ++
            	Store.submissionUpVotes.push(id)
            },


            /**
             *  Downvote submission
             *
             *  @return void
             */
            voteDown () {
				let id = this.list.id

				axios.post('/downvote-submission', {
					submission_id: id,
					previous_vote: this.currentVote
				})

            	// Have down-voted
            	if (this.downvoted) {
            		this.downvoted = false
            		this.list.downvotes --

            		var index = Store.submissionDownVotes.indexOf(id);
                	Store.submissionDownVotes.splice(index, 1);

            		return
            	}

				// Have up-voted
            	if (this.upvoted) {
            		this.upvoted = false
            		this.list.upvotes --

            		var index = Store.submissionUpVotes.indexOf(id);
                	Store.submissionUpVotes.splice(index, 1);
            	}

            	// Not voted
            	this.downvoted = true
            	this.list.downvotes ++
            	Store.submissionDownVotes.push(id)
            },

			showPhotoViewer(index = null){
				if (index !== null) {
					this.photoViewerIndex = index
				}
	            this.photoViewer = true
	        },

			showGifPlayer() {
				console.log('works')
			},

			showEmbed(){
				this.embedViewer = true
			},

			showGifPlayer(){
				this.gifPlayer = true
			},

			closeViwer(){
				this.photoViewer = false
			},

			closeEmbed(){
				this.embedViewer = false
			},

			closeGifPlayer(){
				this.gifPlayer = false
			}
        }
    }
</script>
