<template>
	<section class="container margin-top-1 col-7">
	    <div v-for="submission in submissions" v-bind:key="submission.id">
	        <submission :list="submission"></submission>
	    </div>

		<no-content v-if="nothingFound" :text="'This user has not submitted anything yet'"></no-content>

		<loading v-if="loading"></loading>

	    <no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
</template>

<script>
    import Submission from '../components/Submission.vue'
    import Loading from '../components/Loading.vue'
    import NoContent from '../components/NoContent.vue'
	import NoMoreItems from '../components/NoMoreItems.vue'

    export default {

        components: {
            Submission,
            Loading,
            NoContent,
            NoMoreItems
        },

        data () {
            return {
            	NoMoreItems: false,
	            page: 0,
                submissions: [],
                loading: true,
                nothingFound: false
            }
        },

       created () {
            this.getSubmissions()
			this.$eventHub.$on('scrolled-to-bottom', this.loadMore)
       },

	    watch: {
			'$route': function () {
				this.clearContent()
				this.getSubmissions()
			}
		},


        methods: {
			loadMore () {
				if ( Store.contentRouter == 'content' && !this.loading && !this.NoMoreItems ) {
					this.getSubmissions()
				}
			},

        	/**
        	 * Fetches the submissions via ajax call
        	 *
        	 * @return void
        	 */
        	getSubmissions  () {
				this.page ++
           		this.loading = true

        		axios.post('/downvoted-submissions', {
        			page: this.page
        		}).then((response) => {
					this.submissions = [...this.submissions, ...response.data.data]

					if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

	                if(this.submissions.length < 1) {
	                	this.nothingFound = true
	                }

					this.loading = false
	            })
        	},

            clearContent () {
				this.submissions = []
				this.loading = true
            	this.nothingFound = false
            }
        }
    }
</script>
