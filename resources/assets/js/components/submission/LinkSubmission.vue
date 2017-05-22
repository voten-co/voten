<template>
	<div>
		<div v-if="showBigThumbnail && submission.data.thumbnail" :class="showEmbed ? 'relative pointer' : ''" @click="embedOrOpen">
			<a v-bind:href="submission.data.url" target="_blank" v-if="submission.data.thumbnail">
	            <img v-bind:src="submission.data.thumbnail" v-bind:alt="submission.title" class="big-thumbnail" />
	        </a>

			<span class="play-gif" v-if="showEmbed">
                <i class="v-icon v-play"></i>
            </span>
		</div>

		<div class="link-list-info">
			<a v-bind:href="submission.data.url" target="_blank" class="flex-space">
				<span class="submission-img-title">
					<img v-bind:src="submission.data.thumbnail" v-bind:alt="submission.title"
						v-if="submission.data.thumbnail && showSmallThumbnail" class="small-thumbnail"
						@click="embedOrOpen" :class="showEmbed ? 'pointer' : ''"
					 />

					<span class="v-ultra-bold">
						{{ submission.title }}

						<small class="go-gray">
							 - {{ submission.data.domain }}
						</small>
					</span>
				</span>
			</a>
		</div>
	</div>
</template>

<script>
	import EmbedValidator from '../../mixins/EmbedValidator'

    export default {
		mixins: [EmbedValidator],

        props: ['nsfw', 'submission', 'full'],

		data(){
			return {
				auth
			}
		},

		computed: {
			showBigThumbnail() {
				if (this.full) return true

				if (this.nsfw) return false

				return ! auth.submission_small_thumbnail
			},

			showSmallThumbnail() {
				return ! this.showBigThumbnail && !this.nsfw
			},

			showEmbed() {
				return this.isValidSourceForEmbed && this.submission.data.embed
			}
		},

		methods: {
			/**
			 * It emits the event to open the EmbedViewer if is allowed to. And other
			 * wise it opens the url in a new tab.
			 *
			 * @return void
			 */
			embedOrOpen(event){
				if (this.showEmbed) {
					// prevent the browser from opening the URL
					event.preventDefault()

					// Emit the embed event
					this.$emit('embed')
				}
			}
		}
    }
</script>
