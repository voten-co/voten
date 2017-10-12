<template>
	<section class="bookmarked-items" @scroll="scrolled">
        <submission v-for="submission in submissions" :list="submission" :key="submission.id"></submission>

		<no-content v-if="nothingFound" :text="'You have not bookmarked any submissions yet'"></no-content>

		<loading v-show="loading"></loading>

		<no-more-items :text="'No more items to load'" v-if="NoMoreItems && !nothingFound"></no-more-items>
	</section>
</template>

<script>
    import Loading from '../components/Loading.vue'; 
    import Submission from '../components/Submission.vue'; 
    import NoContent from '../components/NoContent.vue'; 
	import NoMoreItems from '../components/NoMoreItems.vue'; 
	import Helpers from '../mixins/Helpers'; 


    export default {
		mixins: [Helpers], 
		
        components: {
            Loading,
            Submission,
            NoContent,
			NoMoreItems
        },

        data: function () {
            return {
	            NoMoreItems: false,
				loading: true,
                nothingFound: false,
                submissions: [],
				page: 0
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

        	clearContent () {
				this.nothingFound = false
				this.submissions = []
				this.loading = true
        	},

            getSubmissions () {
				this.page ++
           		this.loading = true

            	axios.get('/bookmarked-submissions', {
                	params: {
                        page: this.page
					}
            	}).then((response) => {
					this.submissions = [...this.submissions, ...response.data.data]

					if (response.data.next_page_url == null) {
						this.NoMoreItems = true
					}

					if(this.submissions.length == 0){
						this.nothingFound = true
					}

					this.loading = false
                })
            }
        }
    };
</script>
