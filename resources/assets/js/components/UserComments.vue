<template>
    <section class="container margin-top-1 col-7">
		<section class="box-typical comments" id="comments-section" v-if="comments.length">
	    	<div class="box-typical-inner ui threaded comments">
	    		<div v-for="c in comments">
			        <comment :list="c" :comments-order="'created_at'"></comment>
			    </div>
	    	</div>
	    </section>

        <no-content v-if="nothingFound" :text="'This user has not commented on anything'"></no-content>

        <loading v-if="loading"></loading>

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

        data () {
            return {
            	NoMoreItems: false,
	            page: 0,
                comments: [],
                loading: true,
                nothingFound: false
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
               	this.comments = []
                this.loading = true
                this.nothingFound = false
        	},

        	/**
        	 * Fetches user's comments
        	 *
        	 * @return void
        	 */
        	getComments () {
                this.loading = true
				this.page ++

        		axios.post('/user-comments', {
        			page: this.page,
	    			username: this.$route.params.username
	    		}).then((response) => {
                    this.comments = [...this.comments, ...response.data.data]

                    if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

	                if( this.comments.length < 1 ) {
	                	this.nothingFound = true
	                }

                    this.loading = false
	            });
        	}
        }
    }
</script>
