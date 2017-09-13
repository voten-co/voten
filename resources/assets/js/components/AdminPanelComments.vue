<template>
	<div class="container margin-top-1 col-7">
		<section class="box-typical comments" v-if="comments.length">
			<div class="box-typical-inner ui threaded comments">
				<div v-for="c in uniqueList" :key="c.id" class="v-comment-not-full">
			        <comment :list="c" :comments-order="'created_at'"></comment>
			    </div>
			</div>
		</section>

		<loading v-if="loading"></loading>

		 <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</div>
</template>

<script>
    import Loading from '../components/Loading.vue'
    import Comment from '../components/Comment.vue'
    import NoContent from '../components/NoContent.vue'
    import NoMoreItems from '../components/NoMoreItems.vue'

    export default {
        components: {
        	Loading,
        	Comment,
        	NoContent,
        	NoMoreItems
        },

        data: function () {
            return {
	            NoMoreItems: false,
				loading: true,
                nothingFound: false,
                comments: [],
				page: 0
            }
        },

        created () {
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
            this.getComments()
        },

	    watch: {
			'$route': function () {
				this.clearContent()
				this.getComments()
			}
		},

		computed: {
			/**
			 * Due to the issue with duplicate notifiactions (cuz the present ones have diffrent
			 * timestamps) we need a different approch to make sure the list is always unique.
			 * This ugly coded methods does it! Maybe move this to the Helpers.js mixin?!
			 *
			 * @return array
			 */
			uniqueList() {
				let unique = []
				let temp = []

				this.comments.forEach(function(element, index, self) {
					if (temp.indexOf(element.id) === -1) {
						unique.push(element)
						temp.push(element.id)
					}
				})

				return unique
			}
		},

        methods: {
        	loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems ) {
					this.getComments()
				}
			},

        	clearContent () {
				this.nothingFound = false
				this.users = []
				this.loading = true
        	},

            getComments () {
            	this.loading = true
				this.page ++

                axios.post('/admin/comments', {
                	page: this.page
                }).then((response) => {
	            	this.comments = [...this.comments, ...response.data.data]

					if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

					if(this.comments.length == 0){
						this.nothingFound = true
					}

					this.loading = false
                })
            },

        }
    }
</script>
