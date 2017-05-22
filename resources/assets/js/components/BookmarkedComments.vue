<template>
	<section>
		<section class="box-typical comments" id="comments-section" v-if="comments.length">
	    	<div class="box-typical-inner ui threaded comments">
	    		<div v-for="c in comments" :key="c.id">
	    			<comment :list="c" :comments-order="'created_at'"></comment>
	    		</div>
	    	</div>
	    </section>

		<no-content v-if="nothingFound" :text="'You have not bookmarked any comments yet'"></no-content>

		<loading v-show="loading"></loading>

	    <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
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

            	axios.post('/bookmarked-comments', {
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
            }
        }
    };
</script>
